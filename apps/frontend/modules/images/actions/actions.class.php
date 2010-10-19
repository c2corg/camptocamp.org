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
                    $name[$key] = remove_accents($row['name']);
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
        if ($this->hasRequestParameter('summits') && $summit_ids = explode('-', $this->getRequestParameter('summits')))
        {
            $request_array = array($summit_ids, 'sr', 'ri', 'si');
        }
        elseif ($this->hasRequestParameter('parkings') && $parking_ids = explode('-', $this->getRequestParameter('parkings')))
        {
            $request_array = array($parking_ids, 'pr', 'ri', 'pi');
        }
        elseif ($this->hasRequestParameter('huts') && $hut_ids = explode('-', $this->getRequestParameter('huts')))
        {
            $request_array = array($hut_ids, 'hr', 'ri', 'hi');
        }
        elseif ($this->hasRequestParameter('routes') && $route_ids = explode('-', $this->getRequestParameter('routes')))
        {
            $request_array = array($route_ids, 'ro', 'oi', 'ri');
        }
        elseif ($this->hasRequestParameter('sites') && $site_ids = explode('-', $this->getRequestParameter('sites')))
        {
            $request_array = array($site_ids, 'to', 'oi', 'ti');
        }
        
        if (!empty($request_array))
        {
            $ids = array_shift($request_array);
            $this->pager = new c2cDoctrinePager('Image', (c2cTools::mobileVersion() ? sfConfig::get('app_list_mobile_maxline_number')
                                                                                  : sfConfig::get('app_list_maxline_number')));
            $q = $this->pager->getQuery();
            $q->select('DISTINCT i.id, i.image_type, i.filename, ii.name, ii.culture, ii.search_name')
              ->from('Image i')
              ->leftJoin('i.associations a ON i.id = a.linked_id')
              ->leftJoin('i.ImageI18n ii')
              ->where('(a.main_id IN (SELECT a2.linked_id FROM Association a2 WHERE a2.main_id IN (' . implode(',', $ids). ') AND a2.type = ?) AND a.type = ?)'
                    . ' OR (a.main_id IN (' . implode(',', $ids). ') AND a.type = ?)', $request_array);
            $this->pager->setPage($this->getRequestParameter('page', 1));
            $this->pager->init();
            
            $nb_results = $this->pager->getNbResults();
            $this->nb_results = $nb_results;
            
            if ($nb_results == 0)
            {
                $params_list = array_keys(c2cTools::getAllRequestParameters());
                $params_list = array_diff($params_list, array('module', 'action', 'orderby', 'order', 'npp', 'page', 'format', 'layout'));
                
                if (count($params_list) == 1)
                {
                    $param = reset($params_list);
                    if (strpos($param, 'nam') !== false)
                    {
                        $this->query_string = $this->getRequestParameter($param);
                        $this->setTemplate('../../documents/templates/simplenoresult');
                    }
                }
            }

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
     * Not so good xhtml, but better user experience
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

            ksort($images_uniquenames);
            foreach ($images_uniquenames as $key => $filename)
            {
                $image_type = $images_types[$key];
                $name = $images_names[$key];
                $categories = array_key_exists($key, $images_categories) ?
                              $images_categories[$key] : array();

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
            if ($request->hasParameter('plupload')) $this->plupload = true;
            // display form
        }
    }

    public function handleErrorJsupload()
    {
        // we discard the images, and do redirect to document
        $document_id = $this->getRequestParameter('document_id');
        $mod = $this->getRequestParameter('mod');
        $redir_route = "@document_by_id?module=$mod&id=$document_id";
        return $this->setErrorAndRedirect('image upload failed', $redir_route . '#images');
    }

    // first step of image js upload: upload the images on the server
    public function executeAddtempimages()
    {
        $document_id = $this->getRequestParameter('document_id');
        $mod = $this->getRequestParameter('mod');
        $redir_route = "@document_by_id?module=$mod&id=$document_id";

        $request = $this->getRequest();

        if ($request->getMethod() == sfRequest::POST)
        {
            if (!self::checkUploadRights())
            {
                return $this->setErrorAndRedirect('Operation not allowed', $redir_route);
            }

            $temp_dir = sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR .
                        sfConfig::get('app_images_temp_directory_name') . DIRECTORY_SEPARATOR;
            $uploaded_files = $request->getFiles();

            // We may have more than one image at a time
            $nb_images = count($uploaded_files['image_file']['name']);
            $images = array();
            for ($i=0; $i<$nb_images; $i++)
            {
                $images[$i] = array();
                // custom validation here (in order to only reject images that do not validate)
                $error = '';
                if (!Image::validate_image($uploaded_files['image_file'], $error, $i))
                {
                    $images[$i]['error'] = array('field' => 'image_file', 'msg' => $error);
                    $images[$i]['image_name'] = $uploaded_files['image_file']['name'][$i];
                    continue;
                }

                $img_data = self::handleImage($uploaded_files['image_file']['name'][$i],
                                              $uploaded_files['image_file']['tmp_name'][$i],
                                              $temp_dir, $i);

                if (isset($img_data['error']))
                {
                    $images[$i]['error'] = $img_data['error'];
                    $images[$i]['image_name'] = $uploaded_files['image_file']['name'][$i];
                    continue;
                }
                else // everything went fine
                {
                    $images[$i]['image_filename'] = $img_data['image_filename'];
                    $images[$i]['default_license'] = $img_data['default_license'];
                    $images[$i]['image_number'] = $img_data['image_number'];
                    if (!empty($img_data['image_title']))
                    {
                        $images[$i]['image_title'] = $img_data['image_title'];
                    }
                }
            }
            $this->images = $images;
            $this->setLayout(false);
        }
        else
        {
            return $this->setErrorAndRedirect('Operation not allowed', $redir_route);
        }
    }

    public function handleErrorAddpltempimage()
    {
        $uploaded_files = $this->getRequest()->getFiles();
        $this->image_name = $uploaded_files['image_file']['name'][0];
        $this->setlayout(false);
    }

    /** image uplaod with plupload tool */
    public function executeAddpltempimage()
    {
        $document_id = $this->getRequestParameter('document_id');
        $mod = $this->getRequestParameter('mod');
        $redir_route = "@document_by_id?module=$mod&id=$document_id";

        $request = $this->getRequest();

        if ($request->getMethod() == sfRequest::POST)
        {
            if (!self::checkUploadRights())
            {
                return $this->setErrorAndRedirect('Operation not allowed', $redir_route);
            }

            $temp_dir = sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR .
                        sfConfig::get('app_images_temp_directory_name') . DIRECTORY_SEPARATOR;

            // validation has been done with validator

            $uploaded_file = $request->getFile('image_file');
            $img_data = self::handleImage($this->getRequestParameter('name'),
                                          $uploaded_file['tmp_name'],
                                          $temp_dir);

            if (isset($img_data['error']))
            {
                $this->image_name = $this->getRequestParameter('name');
                $this->setlayout(false);
                $this->getRequest()->setError('image_file', $img_data['error']);
                return sfView::ERROR;
            }

            $this->image_filename = $img_data['image_filename'];
            $this->default_license = $img_data['default_license'];
            $this->image_number = $img_data['image_number'];
            if (!empty($img_data['image_title']))
            {
                $this->image_title = $img_data['image_title'];
            }
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
                $file_ext = Images::detectExtension($filename);
                c2cTools::log("processing file $unique_filename");

                // upload file in a temporary folder
                $new_location = $temp_dir . $unique_filename . $file_ext;
                c2cTools::log("moving file to $new_location");
                if (!move_uploaded_file($filename, $new_location))
                {
                    return $this->setErrorAndRedirect('Failed moving uploaded file', $redir_route);
                }

                if ($file_ext == '.svg')
                {
                    if (!SVG::rasterize($temp_dir, $unique_filename, $file_ext))
                    {
                        return $this->setErrorAndRedirect('Failed rasterizing svg file', $redir_route);
                    }
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

    public function executeDiff()
    {
        $id = $this->getRequestParameter('id');
        $user = $this->getUser();
        $lang = $this->getRequestParameter('lang');
        $document = $this->getDocument($id, $lang);
        $copyright_image = ($document->get('image_type') == 3);
        if (!$user->hasCredential('moderator') && $copyright_image)
        {
            $referer = $this->getRequest()->getReferer();
            $this->setErrorAndRedirect('You do not have the rights to edit this picture', $referer);
        }
        parent::executeDiff();
    }

    public function executeEdit()
    {
        parent::executeEdit();

        // check if the image is already associated to a book (required to decide whether "copyright" is allowed or not)
        $id = $this->document['id'];
        $assocations = Association::findAllAssociations($id, 'bi');
        $this->is_associated_book = !empty($association);
    }

    public function executeHistory()
    {
        $id = $this->getRequestParameter('id');
        $user = $this->getUser();
        $lang = $this->getRequestParameter('lang');
        $document = $this->getDocument($id, $lang);
        $copyright_image = ($document->get('image_type') == 3);
        if (!$user->hasCredential('moderator') && $copyright_image)
        {
            $referer = $this->getRequest()->getReferer();
            $this->setErrorAndRedirect('You do not have the rights to edit this picture', $referer);
        }
        parent::executeHistory();
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
                $path = sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR . 'images';
                Images::removeAll($filename, $path);
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
            case 'inam': return 'mi.search_name';
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
        $params_list = c2cTools::getAllRequestParameters();
        
        return Image::buildListCriteria($params_list);
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
        $this->addParam($out, 'icult');

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
                $article = Document::find('Article', $doc_id);
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

    protected function doMerge($from_id, $document_from, $to_id, $document_to)
    {
        // fetch associated documents before doing the merging as associations will be transferred
        $associations = Association::findAllAssociations($from_id);

        parent::doMerge($from_id, $document_from, $to_id, $document_to);
        // search documents in which from is inserted, and replace the insertion with to

        foreach ($associations as $a)
        {
            $check_id = ($a['main_id'] == $from_id) ? $a['linked_id'] : $check_id = $a['main_id'];
            $check_model = c2cTools::Letter2Model(substr($a['type'],0,1));
            $check_module = c2cTools::Model2Module($check_model);

            $check_doc = Document::find($check_model, $check_id);
            $fields = sfConfig::get('mod_images_bbcode_fields_' . $check_module);

            $languages = $check_doc->getLanguages();
            foreach ($languages as $language)
            {
                $modified = false;
                $conn = sfDoctrine::Connection();
                $conn->beginTransaction();

                $check_doc->setCulture($language);

                foreach ($fields as $field)
                {
                    $text = $check_doc[$field];
                    $edited = preg_replace('#(\[img=\s*)' . $from_id . '([\w\s]*\].*?\[/img\])\n?#is', '${1}' . $to_id . '${2}', $text);
                    $edited = preg_replace('#(\[img=\s*)' . $from_id . '([\w\s]*\/\])\n?#is', '${1}' . $to_id . '${2}', $edited);

                    if ($edited != $text)
                    {
                        $modified = true;
                        $check_doc->set($field, $edited);
                    }
                }

                if ($modified)
                {
                    $history_metadata = new HistoryMetadata();
                    $history_metadata->setComment('Updated image tags');
                    $history_metadata->set('is_minor', true);
                    $history_metadata->set('user_id', sfConfig::get('app_moderator_user_id'));
                    $history_metadata->save();

                    c2cTools::log('After merge of image ' . $from_id . ' into ' . $to_id . ': update image tag for '
                        . strtolower($check_model) . ' ' . $check_id . ' (' . $language . ')');
                    $check_doc->save();
                    $conn->commit();
                    $this->clearCache($check_module, $check_id);
                    $this->clearCache($check_module, $check_id, false, 'view');
                }
                else
                {
                    $conn->rollback();
                }
            }
        }
    }

    private function handleImage($image_name, $tmp_name, $temp_dir, $index = 1)
    {
        // copy the image to the temp directory
        $filename = $tmp_name;
        $unique_filename = c2cTools::generateUniqueName();
        $file_ext = Images::detectExtension($filename);

        $new_location = $temp_dir . $unique_filename . $file_ext;
        if (!move_uploaded_file($filename, $new_location))
        {
            //$images[$i]['image_name'] = $uploaded_files['image_file']['name'][$i];
            //$images[$i]['error'] = array('field' => 'image_file', 'msg' => 'Failed moving uploaded file');
            //continue;
            return array('error' => array('field' => 'image_file', 'msg' => 'Failed moving uploaded file'));
        }

        if ($file_ext == '.svg')
        {
            if (!SVG::rasterize($temp_dir, $unique_filename, $file_ext))
            {
                //$uploaded_files = $this->getRequest()->getFiles();
                //$images[$i]['image_name'] = $uploaded_files['image_file']['name'][$i];
                //$images[$i]['error'] = array('field' => 'image_file', 'msg' => 'Failed rasterizing svg file');
                //continue;
                return array('error' => array('field' => 'image_file', 'msg' => 'Failed rasterizing svg file'));
            }
        }

        // generate thumbnails
        Images::generateThumbnails($unique_filename, $file_ext, $temp_dir);

        // look iptc for a possible title (jpeg only)
        if ($file_ext == '.jpg')
        {
            $size = getimagesize($new_location, $info);
            if (isset($info['APP13']))
            {
                $iptc = iptcparse($info['APP13']);
                if (isset($iptc['2#105'])) // title
                {
                    $image_title = $iptc['2#105'][0];
                }
                else if (isset($iptc['2#120'])) // comment
                {
                    $image_title = $iptc['2#120'][0];
                }
            }
        }

        if (isset($image_title))
        {
            $encoding = mb_detect_encoding($image_title, "UTF-8, ISO-8859-1, ISO-8859-15", true);

            if ($encoding !== false)
            {
                if ($encoding != 'UTF-8')
                {
                    $image_title = mb_convert_encoding($image_title, 'UTF-8', $encoding);
                }
                else
                {
                    $image_title = $image_title;
                }
            }
            // if encoding could not be detected, rather not try to put it as prefilled title
        }

        return array('image_filename' => $unique_filename . $file_ext,
                     'default_license' => $this->getDefaultImageLicense($this->getRequestParameter('document_id'), 
                                                                        $this->getRequestParameter('mod')),
                     'image_number' => (intval($this->getRequestparameter('image_number'))+1)*1000+$index,
                     'image_title' => isset($image_title) ? $image_title : null);
    }

    // check if user has the rights to upload images to the document
    private function checkUploadRights()
    {
        $document_id = $this->getRequestParameter('document_id');
        $mod = $this->getRequestParameter('mod');

        $request = $this->getRequest();

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
        if ((!$user_valid && !$user->hasCredential('moderator'))
            || !$request->hasFiles())
        {
            return false;
        }
        return true;
    }
}
