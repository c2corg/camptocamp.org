<?php
/**
 * images module actions.
 *
 * @package    c2corg
 * @subpackage images
 * @version    $Id: actions.class.php 2542 2007-12-21 19:07:08Z alex $
 */
class imagesActions extends documentsActions
{
    /**
     * Model class name.
     */
    protected $model_class = 'Image';

    public function executeView()
    {
        sfLoader::loadHelpers(array('General', 'MetaLink'));

        parent::executeView();

        // we get the user (id + name) who first uploaded this picture:
        $this->creator = $this->document->getCreator();
        $this->image_type = $this->document['image_type'];

        if (!$this->document->isArchive() && $this->document['redirects_to'] == NULL)
        {
            // here, we add the summit name to route names :        
            $associated_routes = array_filter($this->associated_docs, array('c2cTools', 'is_route'));
            $associated_routes = Route::addBestSummitName($associated_routes, $this->__('&nbsp;:').' ');
            $associated_docs = array_filter($this->associated_docs, array('c2cTools', 'is_not_route'));
            $associated_docs = array_filter($associated_docs, array('c2cTools', 'is_not_image'));
            $associated_docs = array_merge($associated_docs, $associated_routes);

            // sort by document type, name
            if (!empty($associated_docs))
            {
                foreach ($associated_docs as $key => $row)
                {
                    $module[$key] = $row['module'];
                    $name[$key] = search_name($row['name']);
                }
                array_multisort($module, SORT_STRING, $name, SORT_STRING, $associated_docs);
            }
            $this->associated_documents = $associated_docs;

            // link for facebook
            list($image_name, $image_ext) = Images::getFileNameParts($this->document['filename']);
            $image_url = DIRECTORY_SEPARATOR . sfConfig::get('app_upload_dir') . DIRECTORY_SEPARATOR .
                         sfConfig::get('app_images_directory_name') . DIRECTORY_SEPARATOR . $image_name . 'SI' . $image_ext;
            addMetaLink('image_src', $image_url);
        }
    }

    /**  
     * Executes list action.
     * Overrides documentsActions::executeList()
     */
    public function executeList()
    {
        $request_array = array();
        if ($summit_id = $this->getRequestParameter('summits'))
        {
            $request_array = array($summit_id, 'sr', 'ri', $summit_id, 'si');
        }
        elseif ($parking_id = $this->getRequestParameter('parkings'))
        {
            $request_array = array($parking_id, 'pr', 'ri', $parking_id, 'pi');
        }
        elseif ($hut_id = $this->getRequestParameter('huts'))
        {
            $request_array = array($hut_id, 'hr', 'ri', $hut_id, 'hi');
        }
        elseif ($route_id = $this->getRequestParameter('routes'))
        {
            $request_array = array($route_id, 'ro', 'oi', $route_id, 'ri');
        }
        elseif ($site_id = $this->getRequestParameter('sites'))
        {
            $request_array = array($site_id, 'to', 'oi', $site_id, 'ti');
        }
        
        if (!empty($request_array))
        {
            $this->pager = new c2cDoctrinePager('Image', sfConfig::get('app_list_maxline_number'));
            $q = $this->pager->getQuery();
            $q->select('DISTINCT i.id, i.image_type, i.filename, ii.name, ii.culture, ii.search_name')
              ->from('Image i')
              ->leftJoin('i.associations a ON i.id = a.linked_id')
              ->leftJoin('i.ImageI18n ii')
              ->where('(a.main_id IN (SELECT a2.linked_id FROM Association a2 WHERE a2.main_id = ? AND a2.type = ?) AND a.type = ?)'
                    . ' OR (a.main_id = ? AND a.type = ?)', $request_array);
            $this->pager->setPage($this->getRequestParameter('page', 1));
            $this->pager->init();

            $this->setPageTitle($this->__($this->getModuleName() . ' list'));
            $this->setTemplate('list');
        }
        else
        {
            parent::executeList();
            $this->setTemplate('list');
        }
    }

    /**
     * Upload with js
     * Not so good html, but better user experience
     */
    public function executeJsupload()
    {
        $document_id = $this->getRequestParameter('document_id');
        $mod = $this->getRequestParameter('mod');
        $redir_route = "@document_by_id?module=$mod&id=$document_id";
        $request = $this->getRequest();

        if ($request->getMethod() == sfRequest::POST)
        {
            // image files and properties should already have been validated before
            // anyway, we still validate it (everything gets rejected, but they
            // should have had warnings before...

            // check if user has the rights to upload images to the document
            $user = $this->getUser();
            $user_id = $user->getId();
            $user_valid = true;
            switch ($mod)
            {
                case 'users':
                    if ($user_id != $document_id) $user_valid = false;
                    break;
                case 'outings':
                    if (!Association::find($user_id, $document_id, 'uo')) $user_valid = false;
                    break;
                case 'images':
                    $image = Document::find('Image', $document_id, array('image_type'));
                    if (!$image) break;
                    $creator = $image->getCreator();
                    if (($image->get('image_type') == 2) && ($creator['id'] != $user_id)) $user_valid = false;
                    break;
                case 'articles':
                    $article = Document::find('Article', $document_id, array('article_type'));
                    if (($article->get('article_type') == 2) && !Association::find($user_id, $document_id, 'uc')) $user_valid = false;
                    break;
                default:
                    break;
            }

            if (!$user_valid && !$user->hasCredential('moderator'))
            {
                return $this->setErrorAndRedirect('Operation not allowed', $redir_route);
            }

            // move files from temp dir to upload dir
            $temp_dir = sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR .
                        sfConfig::get('app_images_temp_directory_name') . DIRECTORY_SEPARATOR;

            $images_uniquenames = $this->getRequestParameter('image_unique_filename');
            $images_names = $this->getRequestParameter('name');
            $images_categories = $this->hasRequestParameter('categories') ?
                                 $this->getRequestParameter('categories') : array();
            $images_types = $this->getRequestParameter('image_type');

            $activities= array();
            $document = Document::find('Document', $document_id, array('id', 'module'));
            if (!$document) return 0;
            $model = c2cTools::module2model($document->get('module'));
            if (in_array($model, array('Outing', 'Article', 'Book', 'Hut', 'Image', 'Route')))
            {
               $document = Document::find($model, $document_id, array('activities'));
               $activities = $document->get('activities');
            }
            elseif ($model == 'Site')
            {
                $activities = array(4); // rock_climbing for sites by default
            }


            foreach ($images_uniquenames as $key => $filename)
            {
                $image_type = $images_types[$key];
                $name = $images_names[$key];
                $categories = array_key_exists($key, $images_categories) ?
                              $images_categories[$key] : array();
                // TODO check that file exists
                $image_id = Image::customSave($name, $filename,
                                              $document_id, $user_id, $model, $activities, $categories, $image_type);
                $nb_created = gisQuery::createGeoAssociations($image_id, false);
                c2cTools::log("created $nb_created geo associations for image $image_id");
            }

            // remove cache of calling page
            $this->clearCache($mod, $document_id, false, 'view');

            return $this->setNoticeAndRedirect('image successfully uploaded', $redir_route . '#images');
        }
        else
        {
            // display form
        }
    }

    public function handleErrorAddtempimage()
    {
        $this->setlayout(false);
    }

    public function handleErrorJsupload()
    {
        // we discard the images, and do redirect to document // TODO i18n
        $document_id = $this->getRequestParameter('document_id');
        $mod = $this->getRequestParameter('mod');
        $redir_route = "@document_by_id?module=$mod&id=$document_id";
        return $this->setErrorAndRedirect('Image upload failed', $redir_route . '#images');
    }

    // first step of image js upload: upload the image on the server
    public function executeAddtempimage()
    {
        $document_id = $this->getRequestParameter('document_id');
        $mod = $this->getRequestParameter('mod');
        $redir_route = "@document_by_id?module=$mod&id=$document_id";

        $request = $this->getRequest();

        if ($request->getMethod() == sfRequest::POST)
        {
            // check if user has the rights to upload images to the document
            $user = $this->getUser();
            $user_id = $user->getId();
            $user_valid = true;
            switch ($mod)
            {
                case 'users':
                    if ($user_id != $document_id) $user_valid = false;
                    break;
                case 'outings':
                    if (!Association::find($user_id, $document_id, 'uo')) $user_valid = false;
                    break;
                case 'images':
                    $image = Document::find('Image', $document_id, array('image_type'));
                    if (!$image) break;
                    $creator = $image->getCreator();
                    if (($image->get('image_type') == 2) && ($creator['id'] != $user_id)) $user_valid = false;
                    break;
                case 'articles':
                    $article = Document::find('Article', $document_id, array('article_type'));
                    if (($article->get('article_type') == 2) && !Association::find($user_id, $document_id, 'uc')) $user_valid = false;
                    break;
                default:
                    break;
            }

            if (!$user_valid && !$user->hasCredential('moderator'))
            {
                return $this->setErrorAndRedirect('Operation not allowed', $redir_route);
            }

            // copy the image to the temp directory
            $temp_dir = sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR .
                        sfConfig::get('app_images_temp_directory_name') . DIRECTORY_SEPARATOR;
            $uploaded_files = $request->getFiles();
            $filename = $uploaded_files['image_file']['tmp_name'];
            $unique_filename = c2cTools::generateUniqueName();
            $file_ext = Images::detectExtension($filename); // FIXME: use c2cTools::getMimeType or getFileType
            $new_location = $temp_dir . $unique_filename . $file_ext;
            if (!move_uploaded_file($filename, $new_location))
            {
                return $this->setErrorAndRedirect('Failed moving uploaded file', $redir_route);
            }

            // generate thumbnails
            Images::generateThumbnails($unique_filename, $file_ext, $temp_dir);

            $this->image_filename = $unique_filename . $file_ext;
            $this->default_license = $this->getDefaultImageLicense($document_id, $mod);
            $this->image_number = $this->getRequestparameter('image_number'); // TODO check
            $this->setLayout(false);
        }
        else
        {
            return $this->setErrorAndRedirect('Operation not allowed', $redir_route);
        }
    }

    /**
     * Executes easy upload action
     * Due to a limitation in flash... online the name of the file is usable
     * so other informations are sent by reference...
     */
    public function executeUpload()
    {
        $document_id = $this->getRequestParameter('document_id');
        $mod = $this->getRequestParameter('mod');
        $redir_route = "@document_by_id?module=$mod&id=$document_id";
        
        $request = $this->getRequest();
        
        if ($request->getMethod() == sfRequest::POST)
        {
            // check if user has the rights to upload images to the document
            $user = $this->getUser();
            $user_id = $user->getId();
            $user_valid = true;
            switch ($mod)
            {
                case 'users':
                    if ($user_id != $document_id) $user_valid = false;
                    break;
                case 'outings':
                    if (!Association::find($user_id, $document_id, 'uo')) $user_valid = false;
                    break;
                case 'images':
                    $image = Document::find('Image', $document_id, array('image_type'));
                    if (!$image) break;
                    $creator = $image->getCreator();
                    if (($image->get('image_type') == 2) && ($creator['id'] != $user_id)) $user_valid = false;
                    break;
                case 'articles':
                    $article = Document::find('Article', $document_id, array('article_type'));
                    if (($article->get('article_type') == 2) && !Association::find($user_id, $document_id, 'uc')) $user_valid = false;
                    break;
                default:
                    break;
            }

            if (!$user_valid && !$user->hasCredential('moderator'))
            {
                return $this->setErrorAndRedirect('Operation not allowed', $redir_route);
            }

            c2cTools::log('uploading files');

            $temp_dir = sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR . 
                        sfConfig::get('app_images_temp_directory_name') . DIRECTORY_SEPARATOR;
            
            $uploaded_files = $request->getFiles();
            $uploaded_files = $uploaded_files['image_file'];
            $images_names = $this->getRequestParameter('name');
            $images_categories = $this->hasRequestParameter('categories') ?
                                 $this->getRequestParameter('categories') : array();
            $images_types = $this->getRequestParameter('image_type');

            // Note: sfWebRequest::getFile...() methods cannot be used directy since uploaded files
            // are transmitted in a image[] POST var that does not fit with those methods.
            
            foreach ($uploaded_files['tmp_name'] as $key => $filename)
            {
                $unique_filename = c2cTools::generateUniqueName();
                $file_ext = Images::detectExtension($filename); // FIXME: use c2cTools::getMimeType or getFileType
                c2cTools::log("processing file $unique_filename");

                // upload file in a temporary folder
                $new_location = $temp_dir . $unique_filename . $file_ext;
                c2cTools::log("moving file to $new_location");
                if (!move_uploaded_file($filename, $new_location))
                {
                    return $this->setErrorAndRedirect('Failed moving uploaded file', $redir_route);
                }
               
                c2cTools::log('resizing image');
                // generate thumbnails (ie. resized images: "BI"/"SI")
                Images::generateThumbnails($unique_filename, $file_ext, $temp_dir);
              
                // retrieve image caption (name)
                $name = array_key_exists($key, $images_names) ?
                        $images_names[$key] : $this->__('Give me a name');
               
                // save image in DB and move temp files in the main dir
                c2cTools::log('saving image');
                
                $activities= array();
                $document = Document::find('Document', $document_id, array('id', 'module'));
                if (!$document) return 0;
                $model = c2cTools::module2model($document->get('module'));
                
                if (in_array($model, array('Outing', 'Article', 'Book', 'Hut', 'Image', 'Route')))
                {
                    $document = Document::find($model, $document_id, array('activities'));
                    $activities = $document->get('activities');
                }
                elseif ($model == 'Site')
                {
                    $activities = array(4); // rock_climbing for sites by default
                }
                
                $image_type = $images_types[$key];
                $categories = array_key_exists($key, $images_categories) ?
                              $images_categories[$key] : array();
                $image_id = Image::customSave($name, $unique_filename . $file_ext,
                                              $document_id, $user_id, $model, $activities, $categories, $image_type);

                $nb_created = gisQuery::createGeoAssociations($image_id, false);
                c2cTools::log("created $nb_created geo associations for image $image_id");
               // TODO: handle errors with thumbnails generation and data saving?
            }
            
            // remove cache of calling page
            $this->clearCache($mod, $document_id, false, 'view');
            
            return $this->setNoticeAndRedirect('image successfully uploaded', $redir_route . '#images');
        }
        else
        {
            switch ($mod)
            {
                case 'articles':
                    // default license depends on the article type
                    $article = Document::find('Article', $document_id);
                    $this->default_license = $article->get('article_type');
                    break;
                case 'books': $this->default_license = 1; break;
                case 'huts': $this->default_license = 1; break;
                case 'images':
                    // default license is that of associated image
                    $image = Document::find('Image', $document_id);
                    $this->default_license = $image->get('license');
                    break;
                case 'outings': $this->default_license = 2; break;
                case 'parkings': $this->default_license = 1; break;
                case 'routes': $this->default_license = 1; break;
                case 'sites': $this->default_license = 1; break;
                case 'summits': $this->default_license = 1; break;
                case 'users': $this->default_license = 2; break;
                default: $this->default_license = 2;
            }
        }
            
        // display form
    }

    /**
     * filter edits which must require additional parameters 
     * overrides the one in parent class.
     */
    protected function filterAdditionalParameters()
    {
        // images cannot be created via images/edit
        $this->setErrorAndRedirect('You cannot create an image without linking it to an existing document', '@default_index?module=images');
    }
    
    /**
     * deletes linked file 
     * overrides the one in parent class.
     */    
    protected function deleteLinkedFile($id)
    {
        $doc_image = Document::find('Image', $id, array('id', 'filename'));
        if ($doc_image)
        {
            // an image may have more than one linked file (if new version of the image were uploaded)
            $filenames = Image::getLinkedFiles($id);
            foreach ($filenames as $filename)
            {
                list($image_name, $image_ext) = Images::getFileNameParts($filename);

                $path = sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR ;

                $filestodelete = array( $path . $filename, 
                                        $path . $image_name . 'SI' . $image_ext, 
                                        $path . $image_name . 'MI' . $image_ext, 
                                        $path . $image_name . 'BI' . $image_ext); 
                                    
                foreach ($filestodelete as $fn)
                {
                    if (file_exists($fn))
                    {
                        unlink($fn);
                        c2cTools::log("images::deleteLinkedFile unlinked $fn");
                    }
                }
            }
        }
        else
        {
            c2cTools::log("images::deleteLinkedFile failed");
        }
    }

    protected function getSortField($orderby)
    {
        switch ($orderby)
        {
            case 'inam': return 'mi.name';
            case 'act':  return 'm.activities';
            case 'icat':  return 'm.categories';
            case 'auth': return 'm.author';
            case 'anam': return 'ai.name';
            case 'date': return 'm.date_time';
            case 'ityp': return 'm.image_type';
            default: return NULL;
        }
    }

    protected function getListCriteria()
    {
        $conditions = $values = array();

        $this->buildCondition($conditions, $values, 'Multilist', array('g', 'linked_id'), 'areas', 'join_area');
        $this->buildCondition($conditions, $values, 'String', 'mi.search_name', array('inam', 'name'));
    //    $this->buildCondition($conditions, $values, 'String', 'si.search_name', 'auth');
        $this->buildCondition($conditions, $values, 'Array', 'categories', 'icat');
        $this->buildCondition($conditions, $values, 'Array', 'activities', 'act');
        $this->buildCondition($conditions, $values, 'Date', 'date_time', 'date');
        $this->buildCondition($conditions, $values, 'Item', 'm.image_type', 'ityp');
        $this->buildCondition($conditions, $values, 'Georef', null, 'geom');
        $this->buildCondition($conditions, $values, 'List', 'm.id', 'id');
        
        $this->buildCondition($conditions, $values, 'List', 'd.main_id', 'documents', 'join_doc');
        
        $this->buildCondition($conditions, $values, 'List', 'hm.user_id', 'user', 'join_user'); // TODO here we should restrict to initial uploader (ticket #333)
        $this->buildCondition($conditions, $values, 'List', 'hm.user_id', 'users', 'join_user'); // TODO here we should restrict to initial uploader (ticket #333)

        if (!empty($conditions))
        {
            return array($conditions, $values);
        }

        return array();
    }

    protected function filterSearchParameters()
    {
        $out = array();

        $this->addListParam($out, 'areas');
        $this->addNameParam($out, 'inam');
        $this->addListParam($out, 'icat');
        $this->addListParam($out, 'act');
        $this->addNameParam($out, 'auth');
        $this->addDateParam($out, 'date');
        $this->addParam($out, 'geom');
        $this->addParam($out, 'ityp');

        return $out;
    }

    /**
     * filter for people who have the right to edit current document (linked people for outings, original editor for articles ....)
     * overrides the one in parent class.
     */
    protected function filterAuthorizedPeople($id)
    {
        // we know here that document $id exists and that its model is the current one (Image).
        // we must guess the associated people and restrain edit rights to these people + moderator + creator of the image in the
        // case of a personal content.
        // for collaborative content, everybody is allowed to edit the image

        $user = $this->getUser();
        $creator = $this->document->getCreator(); 
        $id = $this->getRequestParameter('id');
        $lang = $this->getRequestParameter('lang');
        $document = $this->getDocument($id, $lang);
        $collaborative_image = ($document->get('image_type') == 1);

        if (!$user->hasCredential('moderator') && $user->getId() != $creator['id'] && !$collaborative_image)
        {
            $referer = $this->getRequest()->getReferer();
            $this->setErrorAndRedirect('You do not have the rights to edit this picture', $referer);
        }
    }

    public function handleErrorFilterredirect()
    {
        $this->forward('outings', 'filter');
    }

    private function getDefaultImageLicense($doc_id, $doc_module)
    {
        switch ($doc_module)
        {
            case 'articles':
                // default license depends on the article type
                $article = Document::find('Article', $document_id);
                $default_license = $article->get('article_type');
                break;
            case 'books':
            case 'huts':
            case 'parkings':
            case 'routes':
            case 'sites':
            case 'summits':
                $default_license = 1; break;
            case 'images':
                // default license is that of associated image
                $image = Document::find('Image', $document_id);
                $default_license = $image->get('license');
                break;
            case 'outings':
            case 'users':
            default:
                $default_license = 2; break;
        }
        return $default_license;
    }
}
