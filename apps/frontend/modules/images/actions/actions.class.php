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
            $associated_routes = Route::addBestSummitName($associated_routes, $this->__(' :').' ');
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
            $this->associated_docs = $associated_docs;

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
        if ($summit_id = $this->getRequestParameter('summit'))
        {
            $request_array = array($summit_id, 'sr', 'ri', $summit_id, 'si');
        }
        elseif ($parking_id = $this->getRequestParameter('parking'))
        {
            $request_array = array($parking_id, 'pr', 'ri', $parking_id, 'pi');
        }
        elseif ($hut_id = $this->getRequestParameter('hut'))
        {
            $request_array = array($hut_id, 'hr', 'ri', $hut_id, 'hi');
        }
        elseif ($route_id = $this->getRequestParameter('route'))
        {
            $request_array = array($route_id, 'ro', 'oi', $route_id, 'ri');
        }
        elseif ($site_id = $this->getRequestParameter('site'))
        {
            $request_array = array($site_id, 'to', 'oi', $site_id, 'ti');
        }
        
        if (!empty($request_array))
        {
            $this->pager = new c2cDoctrinePager('Image', sfConfig::get('app_list_maxline_number'));
            $q = $this->pager->getQuery();
            $q->select('DISTINCT i.id, i.filename, ii.name, ii.culture, ii.search_name')
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

        $request = $this->getRequest();

        if ($request->getMethod() == sfRequest::POST)
        {
            // TODO
            $this->setLayout(false);
            return $this->renderText('plop');
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
     * Executes "unlink with document" action
     * ... restricted in security.yml to moderators
     */
    public function executeUnlink()
    {
        $referer = $this->getRequest()->getReferer();
        $user_id = $this->getUser()->getId();
        $image_id = $this->getRequestParameter('image_id');
        $document_id = $this->getRequestParameter('document_id');
        
        $document = Document::find('Document', $document_id, array('id', 'module', 'is_protected'));
        
        if (!$document)
        {
            return $this->setWarningAndRedirect('Document does not exist', $referer);
        }
        
        if ($document->get('is_protected'))
        {
            return $this->setWarningAndRedirect('Document is protected', $referer);
        }
        
        $document_module = $document->get('module');
        
        // association is not strict (ie image can be on both side of n-n relation) for image-image relationships.
        $strict = !($document_module == 'images');
        // check whether association has already been done or not:
        $type = c2cTools::Model2Letter(c2cTools::module2model($document_module)).'i';
        
        $a = Association::find($document_id, $image_id, $type, $strict);
        
        $conn = sfDoctrine::Connection();
        try
        {
            $conn->beginTransaction();
                    
            $a->delete();
                    
            $al = new AssociationLog();
            $al->main_id = $document_id;
            $al->linked_id = $image_id;
            $al->type = $type;
            $al->user_id = $user_id;
            $al->is_creation = 'false';
            $al->save();
            
            $conn->commit();
        }
        catch (exception $e)
        {
            $conn->rollback();
            c2cTools::log("executeUnlink() : Association deletion + log failed ($document_id, $image_id, $type, $user_id) - rollback");
            return $this->setWarningAndRedirect('Association deletion failed', $referer);
        }
                
        // cache clearing for current doc in every lang:
        $this->clearCache('images', $image_id, false, 'view');
        $this->clearCache($document_module, $document_id, false, 'view');
        
        // set flash info:
        return $this->setNoticeAndRedirect('Image has been unlinked', $referer . '#images');
    }

    /**
     * Executes "associate image with document" action
     * ... restricted in security.yml to logged people
     */
    public function executeAddassociation()
    {
        sfLoader::loadHelpers(array('General'));

        if (!$this->hasRequestParameter('document_id') ||
            !$this->hasRequestParameter('document_module') ||
            !$this->hasRequestParameter('image_id'))
        {
            return $this->ajax_feedback('Operation not allowed');
        }

        $image_id = $this->getRequestParameter('image_id');
        $document_id = $this->getRequestParameter('document_id');
        $module = $this->getRequestParameter('document_module');

        switch ($module)
        {
            case 'articles': $fields = array('id', 'is_protected', 'article_type'); break;
            case 'images': $fields = array('id', 'is_protected', 'image_type'); break;
            case 'documents': $fields = array('id', 'is_protected', 'module'); break; // FIXME prevent such case?
            default: $fields = array('id', 'is_protected'); break;
        }

        $document = Document::find(c2cTools::module2model($module), $document_id, $fields);
        $module = isset($module) ? $module : $document->get('module');

        $user = $this->getUser();
        $user_id = $user->getId();

        if (!$document)
        {
            return $this->ajax_feedback('Document does not exist');
        }

        if ($document->get('is_protected'))
        {
            return $this->ajax_feedback('Document is protected');
        }

        // Check rights if document is outing, user profile, personal article or personal image
        if (!$user->hasCredential('moderator'))
        {
            if ($module == 'users' && $document_id != $user_id)
            {
                return $this->ajax_feedback('You do not have the right to link an image to another user profile');
            }
            if (($module == 'outings') && (!Association::find($user_id, $document_id, 'uo')))
            {
                return $this->ajax_feedback('You do not have the right to link an image to another user outing');
            }
            if (($module == 'articles') && ($document->get('article_type') == 2) && (!Association::find($user_id, $document_id, 'uc')))
            {
                return $this->ajax_feedback('You do not have the right to link an image to a personal article');
            }
            if (($module == 'images') && ($document->get('image_type') == 2) && ($document->getCreator() != $user_id))
            {
                return $this->ajax_feedback('You do not have the right to link an image to a personal image');
            }
        }

        $image = Document::find('Image', $image_id, array('id', 'is_protected', 'image_type'));

        if (!$image)
        {
            return $this->ajax_feedback('Image does not exist');
        }

        if ($image->get('is_protected'))
        {
            return $this->ajax_feedback('Image is protected');
        }

        $type = c2cTools::Model2Letter(c2cTools::module2model($module)).'i';
        $di = new Association;
        $status = $di->doSaveWithValues($document_id, $image_id, $type, $user_id);

        if (!$status)
        {
            return $this->ajax_feedback('Could not perform association');
        }
        
        $module_name = $this->__($module);

        // cache clearing for current doc in every lang:
        $this->clearCache('images', $image_id, false, 'view');
        $this->clearCache($module, $document_id, false, 'view');
        
        $document->setBestName($user->getPreferedLanguageList());
                
        if ($module == 'routes') 
        {
            $summit = explode(' [',$this->getRequestParameter('summits_name'));
            $bestname = $summit[0] . $this->__('&nbsp;:') . ' ' . $document->get('name');
        }
        else
        {
            $bestname = $document->get('name');
        } 

        sfLoader::loadHelpers(array('Tag', 'Url', 'Asset'));
        $out = '<li>'. picto_tag('picto_' . $module, $module_name) . 
               ' ' . link_to($bestname, "@document_by_id?module=$module&id=$document_id") . '</li>';

        return $this->renderText($out);
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
            case 'cat':  return 'm.categories';
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
        $this->buildCondition($conditions, $values, 'Array', 'categories', 'cat');
        $this->buildCondition($conditions, $values, 'Array', 'activities', 'act');
        $this->buildCondition($conditions, $values, 'Date', 'date_time', 'date');
        $this->buildCondition($conditions, $values, 'Item', 'm.image_type', 'ityp');
        $this->buildCondition($conditions, $values, 'Georef', null, 'geom');
        
        $this->buildCondition($conditions, $values, 'List', 'hm.user_id', 'user', 'join_user'); // TODO here we should restrict to initial uploader (ticket #333)

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
        $this->addListParam($out, 'cat');
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
}
