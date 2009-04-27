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
        sfLoader::loadHelpers(array('General'));

        parent::executeView();

        // we get the user (id + name) who first uploaded this picture:
        $this->creator = $this->document->getCreator();

        if (!$this->document->isArchive())
        {
            // here, we add the summit name to route names :        
            $associated_routes = array_filter($this->associated_docs, array('c2cTools', 'is_route'));
            $associated_routes = Route::addBestSummitName($associated_routes);
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
        }
    }

    /**  
     * Executes list action.
     * Overrides documentsActions::executeList()
     */
    public function executeList()
    {    
        parent::executeList();
        $this->setTemplate('list');
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
            c2cTools::log('uploading files');

            $temp_dir = sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR . 
                        sfConfig::get('app_images_temp_directory_name') . DIRECTORY_SEPARATOR;
            
            $uploaded_files = $request->getFiles();
            $uploaded_files = $uploaded_files['image_file'];
            $images_names = $this->getRequestParameter('name');
            $images_categories = $this->hasRequestParameter('categories') ?
                                 $this->getRequestParameter('categories') : array();

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
               
                $user_id = $this->getUser()->getId();
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
                
                $categories = array_key_exists($key, $images_categories) ?
                              $images_categories[$key] : array();
                $image_id = Image::customSave($name, $unique_filename . $file_ext,
                                                $document_id, $user_id, $model, $activities, $categories);

                $nb_created = gisQuery::createGeoAssociations($image_id, false);
                c2cTools::log("created $nb_created geo associations for image $image_id");
               // TODO: handle errors with thumbnails generation and data saving?
            }
            
            // remove cache of calling page
            $this->clearCache($mod, $document_id, false, 'view');
            
            return $this->setNoticeAndRedirect('image successfully uploaded', $redir_route . '#images');
        }
            
        // display form
    }

    /**
     * Overloaded method from documentsActions class.
     */
    protected function setDataFields($document)
    {    
        foreach (Document::getVisibleFieldNamesByModel($this->model_class) as $field_name)
        {
            $field_value = $this->getRequestParameter($field_name);
            if ($field_name == 'filename' && empty($field_value))
            {
                continue;
            }

            $document->set($field_name, $field_value);
        }
        
        // if at least one of the field lon or lat is empty, then overwrite whole geo data by those taken from EXIF stamp.
        $overwrite_geom = !($document->get('lon') && $document->get('lat'));
        
        // Also read exif data in image... and set the corresponding fields accordingly.
        $document->populateWithExifDataFrom(sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR . 
                                            sfConfig::get('app_images_directory_name') . DIRECTORY_SEPARATOR . 
                                            $document->get('filename'), $overwrite_geom, false);
    }

    /**
     * Overloaded method from documentsActions class.
     */
    protected function isUnModified()
    {
        // these values are only loaded from exif, so having them
        // doesn't mean something has been modified
        $exif_only_keys = array('camera_name' => 1,
                                'exposure_time' => 1,
                                'fnumber' => 1,
                                'iso_speed' => 1,
                                'focal_length' => 1);
        $modified = $this->document->getModified();
        return (count($modified) - count(array_intersect_key($modified, $exif_only_keys)) == 0 &&
                count($this->document->getCurrentI18nObject()->getModified()) == 0);
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
        $image_id = $this->getRequestParameter('image_id');
        $document_id = $this->getRequestParameter('document_id');

        $document = Document::find('Document', $document_id, array('id', 'module', 'is_protected'));
        $module = $document->get('module');

        $user = $this->getUser();
        $user_id = $user->getId();

        if ($module == 'users' && $document_id != $user_id)
        {
            return $this->ajax_feedback('You do not have the right to edit another user profile');
        }

        if (!$document)
        {
            return $this->ajax_feedback('Document does not exist');
        }
        
        if ($document->get('is_protected'))
        {
            return $this->ajax_feedback('Document is protected');
        }

        $image = Document::find('Image', $image_id, array('id', 'is_protected'));

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
            $bestname = $summit[0] . ' : ' . $document->get('name');
        }
        else
        {
            $bestname = $document->get('name');
        } 

        sfLoader::loadHelpers(array('Tag', 'Url', 'Asset'));
        $out = '<li>'. image_tag(sfConfig::get('app_static_url') . '/static/images/modules/' . $module . '_mini.png', 
                                 array('alt' => $module_name, 'title' => $module_name)) . 
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
            $filename = $doc_image->get('filename');
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
            default: return NULL;
        }
    }

    protected function getListCriteria()
    {
        $conditions = $values = array();

        $this->buildCondition($conditions, $values, 'List', 'ai.id', 'areas');
        $this->buildCondition($conditions, $values, 'String', 'mi.search_name', array('inam', 'name'));
    //    $this->buildCondition($conditions, $values, 'String', 'si.search_name', 'auth');
        $this->buildCondition($conditions, $values, 'Array', 'categories', 'cat');
        $this->buildCondition($conditions, $values, 'Array', 'activities', 'act');
        $this->buildCondition($conditions, $values, 'Compare', 'm.date_time', 'date');
        $this->buildCondition($conditions, $values, 'Georef', null, 'geom');

        if ($user = $this->getRequestParameter('user'))
        {
            $conditions[] = 'v.version = 1 AND hm.user_id = ?';
            $values[] = $user;
            $conditions['join_user'] = true;
        }

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
        $this->addNameParam($out, 'auth');
        $this->addListParam($out, 'cat');
        $this->addParam($out, 'geom');
        $this->addListParam($out, 'act');
        $this->addDateParam($out, 'date');

        return $out;
    }

    /**
     * filter for people who have the right to edit current document (linked people for outings, original editor for articles ....)
     * overrides the one in parent class.
     */
    protected function filterAuthorizedPeople($id)
    {
        // we know here that document $id exists and that its model is the current one (Image).
        // we restrain edit rights to moderator + creator of the image

        $user = $this->getUser();
        $creator = $this->document->getCreator();

        if (!$user->hasCredential('moderator') && $user->getId() != $creator['id'])
        {
            $referer = $this->getRequest()->getReferer();
            $this->setErrorAndRedirect('You do not have the rights to edit this picture', $referer);
        }
    }
}
