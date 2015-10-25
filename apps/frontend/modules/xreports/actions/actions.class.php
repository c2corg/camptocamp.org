<?php
/**
 * xreports module actions.
 *
 * @package    c2corg
 * @subpackage xreports
  */
class xreportsActions extends documentsActions
{
    /**
     * Model class name.
     */
    protected $model_class = 'Xreport';

    /**
     * Executes view action.
     */
    public function executeView()
    {
        parent::executeView();

        $title = $this->document->get('name');
        $this->setPageTitle($title);
        
        if (!$this->document->isArchive() && $this->document['redirects_to'] == NULL)
        {
            $user = $this->getUser();
            $prefered_cultures = $user->getCulturesForDocuments();

            // associated users
            $associated_users = array_filter($this->associated_docs, array('c2cTools', 'is_user'));
            $this->associated_users = $associated_users;
            
            // associated routes
            $associated_routes = Route::getAssociatedRoutesData($this->associated_docs, $this->__(' :').' ');
            $this->associated_routes = $associated_routes;
            
            // associated outings
            $associated_outings = array_filter($this->associated_docs, array('c2cTools', 'is_outing'));
            $this->associated_outings = $associated_outings;
            
            $related_portals = array();
            Portal::getLocalPortals($related_portals, $this->associated_areas);
            $this->related_portals = $related_portals;

            $doc_name = $this->document->get('name');
            $description = array($doc_name, $this->getAreasList());
            $this->getResponse()->addMeta('description', implode(' - ', $description));
        }
    }

    public function executePreview()
    {
        parent::executePreview();
        $this->setTemplate('../../xreports/templates/preview');
    }

    public function executeDiff()
    {
        $id = $this->getRequestParameter('id');
        $this->filterAuthorizedPeople($id);
        parent::executeDiff();
    }

    public function executeHistory()
    {
        $id = $this->getRequestParameter('id');
        $this->filterAuthorizedPeople($id);
        parent::executeHistory();
    }

    protected function endEdit()
    {
        //Test if form is submitted or not
        if ($this->success) // form submitted and success (doc has been saved)
        {
            // if this is the first version of the outing (aka creation)
            // set a flash message to encourage to also enhance the corresponding route
            if (is_null($this->document->getVersion()))
            {
                $this->setNotice('thanks for new xreport');
            }

            // try to perform association with linked_doc (if pertinent)
            $associated_id = $this->getRequestParameter('document_id');
            $user_id = $this->getUser()->getId();
            $id = $this->document->get('id');
        
            if (($this->new_document && $associated_id ) || ($associated_id && !Association::find($associated_id, $id)))  // can be a 'tx' or 'rx' Association
            {
                // we must get this document's module (site or route ?)
                $associated_doc = Document::find('Document', $associated_id, array('module'));
                if ($associated_doc) 
                {
                    $associated_module = $associated_doc->get('module');
            
                    $a = new Association();
                    if ($associated_module == 'routes')
                    {
                        $a->doSaveWithValues($associated_id, $id, 'rx', $user_id); // main, linked, type
                    }
                    elseif ($associated_module == 'sites')
                    {
                        $a->doSaveWithValues($associated_id, $id, 'tx', $user_id); // main, linked, type
                    }
                    elseif ($associated_module == 'outings')
                    {
                        $a->doSaveWithValues($associated_id, $id, 'ox', $user_id); // main, linked, type
                        // clear cache of associated site ...
                        $this->clearCache('outings', $associated_id, false, 'view');
                    }
                    
                    // here if we have created a new document and if $this->document->get('geom_wkt') is null, then use associated doc geom associations:
                    // this allows us to filter on ranges even if no GPX is uploaded
                    if ($this->new_document && $associated_id && !$this->document->get('geom_wkt'))
                    {
                        // get all associated regions (only regions, countries, depts, no maps !) with this summit:
                        $associations = GeoAssociation::findAllAssociations($associated_id, array('dr', 'dc', 'dd', 'dv'));
                        // replicate them with xreport_id instead of (route_id or site_id or outing_id):
                        foreach ($associations as $ea)
                        {
                            $areas_id = $ea->get('linked_id');
                            $a = new GeoAssociation();
                            $a->doSaveWithValues($id, $areas_id, $ea->get('type'));
                        }
                    }
                }
            }    

            // create also association with current user.
            if ($this->new_document)
            {
                $uo = new Association();
                $uo->doSaveWithValues($user_id, $id, 'ux', $user_id); // main, linked, type
            }    
            
            // create association with MW contest article, if requested
            if ($this->new_document)
            {
                $mw_contest_associate = $this->getRequestParameter('mw_contest_associate');
                if ($mw_contest_associate)
                {
                    $mw_article_id = sfConfig::get('app_mw_contest_id');
                    $oc = new Association();
                    $oc->doSaveWithValues($id, $mw_article_id, 'oc', $user_id);
                }
            }
            
            parent::endEdit(); // redirect to document view
        }
    }

    /**
     * populates custom fields (for instance if we are creating a new outing, already associated with a route)
     * overrides the one in documentsActions class.
     */
    protected function populateCustomFields()
    {
        $document = $this->document;
 
        if ($this->getRequestParameter('link') && 
            $linked_doc = Document::find('Document', $this->getRequestParameter('link'), array('module')))
        {
            $prefered_cultures = $this->getUser()->getCulturesForDocuments();
            switch ($linked_doc->get('module'))
            {
                case 'routes':
                    $linked_doc = Document::find('Route', $this->getRequestParameter('link'), array('activities'));

                    $linked_doc->setBestCulture($prefered_cultures);
                    $this->linked_doc = $linked_doc;
            
                    // FIXME: this "field getting" triggers an additional request to the db (yet already hydrated), 
                    // probably because activities field has been hydrated into object as a string and not as an array
                    // cf filterGetActivities and filterSetActivities in Route.class.php ...
                    $activities = $linked_doc->get('activities');
                    $document->set('activities', $activities);
            
                    // find associated summits to this route, extract the highest and create document with this name.
                    $associated_summits = array_filter(Association::findAllWithBestName($linked_doc->get('id'), $prefered_cultures), array('c2cTools', 'is_summit'));
 
                    $this->highest_summit_name = c2cTools::extractHighestName($associated_summits);
                    $document->set('name', $this->highest_summit_name . $this->__(' :') . ' ' . $linked_doc->get('name'));
                    
                    break;
            
                case 'sites':
                    $linked_doc = Document::find('Site', $this->getRequestParameter('link'), array('mean_rating'));
                    $linked_doc->setBestCulture($prefered_cultures);
                    $document->set('name', $linked_doc->get('name'));
                    $document->set('activities', array(4));
                    $this->linked_doc = $linked_doc;
                    break;
                
                case 'outings':
                    $linked_doc = Document::find('Outing', $this->getRequestParameter('link'), array('activities'));

                    $linked_doc->setBestCulture($prefered_cultures);
                    $this->linked_doc = $linked_doc;
                    $document->set('name', $linked_doc->get('name'));
            
                    // FIXME: this "field getting" triggers an additional request to the db (yet already hydrated), 
                    // probably because activities field has been hydrated into object as a string and not as an array
                    // cf filterGetActivities and filterSetActivities in Route.class.php ...
                    $activities = $linked_doc->get('activities');
                    $document->set('activities', $activities);
                    
                    break;
            
                default:
                    break;
            }   
        }
        $this->document = $document;
    }
    
    /**
     * filter for people who have the right to edit current document (linked people for outings, original editor for articles ....)
     * overrides the one in parent class.
     */
    protected function filterAuthorizedPeople($id)
    {
        // we know here that document $id exists and that its model is the current one (Outing).
        // we must guess the associated people and restrain edit rights to these people + moderator.

        $user = $this->getUser();
        $a = Association::find($user->getId(), $id, 'ux');
        
        if (!$a && !$user->hasCredential('moderator'))
        {
            $referer = $this->getRequest()->getReferer();
            $this->setErrorAndRedirect('You do not have the rights to edit this xreport', $referer);
        }
    }
    
    /**
     * filter edits which must require additional parameters (link for instance : xreport with route or with site)
     * overrides the one in parent class.
     */
    protected function filterAdditionalParameters()
    {
        $id = $this->getRequestParameter('link', 0) + $this->getRequestParameter('document_id', 0);
        
        // linked_doc already retrieved in populateCustomFields() except when creating a new xreport
        if (isset($this->linked_doc))
        {
            $linked_doc = $this->linked_doc;
        }
        elseif ($id)
        {
            // route (most of the time) or site
            $linked_doc = Document::find('Route', $id, array('id', 'module'));
            if (!$linked_doc)
            {
                $linked_doc = Document::find('Site', $id, array('id', 'module'));
            }
            if (!$linked_doc)
            {
                $linked_doc = Document::find('Outing', $id, array('id', 'module'));
            }
        }

        if ($linked_doc && $linked_doc->get('module') == 'routes')
        {
            if ($this->document)
            {
	              $linked_doc->set('name', $this->document->get('name')); // contains highest summit too
            }
        }
    }

    /**
     * Parses REQUEST sent by filter form and keeps only relevant parameters.
     * @return array
     */
    protected function filterSearchParameters()
    {
        $out = array();
        
        if($this->getUser()->isConnected())
        {
            $myxreports = $this->getRequestParameter('myxreports', 0);
            if ($myxreports == 1)
            {
                $user_id = $this->getUser()->getId();
                $out[] = "users=$user_id";
            }
        }

        $this->addListParam($out, 'areas');
        $this->addAroundParam($out, 'xarnd');
        
        $this->addNameParam($out, 'xnam');
        $this->addListParam($out, 'act');

        $this->addCompareParam($out, 'xalt');
        $this->addCompareParam($out, 'xpar');
        $this->addCompareParam($out, 'ximp');
        $this->addListParam($out, 'xsev');
        $this->addParam($out, 'xres');
        $this->addListParam($out, 'xtyp');
        $this->addDateParam($out, 'date');

        $this->addParam($out, 'geom');
        $this->addParam($out, 'xcult');

        return $out;
    }

    /**
     * Executes list action
     */
    public function executeList()
    {
        parent::executeList();

        $nb_results = $this->nb_results;
        if ($nb_results == 0) return;

        $timer = new sfTimer();
        $xreports = $this->query->execute(array(), Doctrine::FETCH_ARRAY);
        c2cActions::statsdTiming('pager.getResults', $timer->getElapsedTime());

        Area::sortAssociatedAreas($xreports);

        $this->items = Language::parseListItems($xreports, 'Xreport');
    }

    public function executeMyXreports()
    {
        // redirect to user xreports list if connected
        if($this->getUser()->isConnected())
        {
            $user_id = $this->getUser()->getId();
            $this->redirect('@default?module=xreports&action=list&users='.$user_id.'&orderby=date&order=desc');
        }
        else
        {
            sfLoader::loadHelpers('Url');
            $this->redirect(url_for('@login', true).'?redirect=xreports/myxreports');
        }
    }
}
