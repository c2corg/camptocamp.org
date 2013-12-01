<?php
/**
 * articles module actions.
 *
 * @package    c2corg
 * @subpackage articles
 * @version    $Id: actions.class.php 1132 2007-08-01 14:38:06Z fvanderbiest $
 */
class articlesActions extends documentsActions
{
    /**
     * Model class name.
     */
    protected $model_class = 'Article';
    
    /**
     * Executes view action.
     */
    public function executeView()
    {
        sfLoader::loadHelpers(array('General'));

        parent::executeView();
        
        if (!$this->document->isArchive() && $this->document['redirects_to'] == NULL)
        {
            // Add the summit name to route
            $associated_routes = array_filter($this->associated_docs, array('c2cTools', 'is_route'));
            $associated_routes = Route::addBestSummitName($associated_routes, $this->__(' :').' ');

            // Include outings additional data
            $associated_outings = array_filter($this->associated_docs, array('c2cTools', 'is_outing'));
            $associated_outings = Outing::fetchAdditionalFields($associated_outings);

            // group all linked docs except images in a single list
            $associated_docs = array_filter($this->associated_docs, array('c2cTools', 'is_not_route'));
            $associated_docs = array_filter($associated_docs, array('c2cTools', 'is_not_outing'));
            $associated_docs = array_filter($associated_docs, array('c2cTools', 'is_not_image'));
            $associated_docs = array_merge($associated_docs, $associated_routes, $associated_outings);
    
            // sort by document type, name
            if (count($associated_docs))
            {
                foreach ($associated_docs as $key => $row)
                {
                    $module[$key] = $row['module'];
                    $name[$key] = remove_accents($row['name']);
                }
                array_multisort($module, SORT_STRING, $name, SORT_STRING, $associated_docs);
            }
            $this->associated_users = array_filter($associated_docs, array('c2cTools', 'is_user'));
            $this->associated_documents = $associated_docs;
            
            // Retrieve linked docs areas (except users)
            $parent_ids = array();
            $associated_areas = array();
            foreach ($this->associated_docs as $doc)
            {
                if ($doc['module'] != 'users')
                {
                    $parent_ids[] = $doc['id'];
                }
            }
            if (count($parent_ids))
            {
                $prefered_cultures = $this->getUser()->getCulturesForDocuments();
                $associated_areas = GeoAssociation::findAreasWithBestName($parent_ids, $prefered_cultures);
            }
            $this->associated_areas = $associated_areas;

            // retrieve related portals, depending on article category
            // Soft mobility articles => changedapproche
            // ice activity + gear, stories or expeditions => ice portal
            $related_portals = array();
            $activities = $this->document->get('activities');
            $categories = $this->document->get('categories');
            if (in_array(7, $categories))
            {
                $related_portals[] = 'cda';
            }
            if (array_intersect(array(2, 3, 8), $categories) && in_array(5, $activities))
            {
                $related_portals[] = 'ice';
            }
            Portal::getLocalPortals($related_portals, $associated_areas);
            $this->related_portals = $related_portals;
    
            sfLoader::loadHelpers(array('sfBBCode', 'SmartFormat'));
            $abstract = strip_tags(parse_links(parse_bbcode_abstract($this->document->get('abstract'))));
            $this->getResponse()->addMeta('description', $abstract);
            if (in_array(100, $categories))
            {
                $this->getResponse()->addMeta('robots', 'noindex, follow');
            }
        }
    }
    
    protected function endEdit()
    {
        if ($this->success) // form submitted and success (doc has been saved)
        {
            // create also association with current user (only if this is a personal article)
            if ($this->new_document && ($this->document->get('article_type') == 2))
            {
                $user_id = $this->getUser()->getId();
                $uc = new Association();
                $uc->doSaveWithValues($user_id, $this->document->get('id'), 'uc', $user_id); // main, linked, type
            }    
            
            parent::endEdit(); // redirect to document view
        }
    }
    
    /**
     * filter for people who have the right to edit current document (linked people for outings, original editor for articles ....)
     * overrides the one in parent class. Triggered upon existing document editing.
     */
    protected function filterAuthorizedPeople($id)
    {
        $article = $this->document;

        // for an unknow reason, we have to double here this protection, which already exists at "document" level
        if ($article->get('is_protected') == true)
        {
            $referer = $this->getRequest()->getReferer();
            $this->setErrorAndRedirect('You cannot edit a protected document', $referer);
        }
        
        if ($article && ($type = $article->get('article_type') == 2))
        {
            // personal article
            // these articles are only editable by associated authors + moderators
            $user = $this->getUser();
            $a = Association::find($user->getId(), $id, 'uc'); // user-article
            if (!$a && !$user->hasCredential('moderator'))
            {
                $referer = $this->getRequest()->getReferer();
                $this->setErrorAndRedirect('You do not have the rights to edit this article', $referer);
            }
        }
    }

    protected function filterSearchParameters()
    {
        $out = array();

        $this->addListParam($out, 'areas');
        
        $this->addNameParam($out, 'cnam');
        $this->addListParam($out, 'ccat');
        $this->addListParam($out, 'act');
        $this->addParam($out, 'ctyp');
        $this->addParam($out, 'ccult');

        return $out;
    }

    public function executeMyArticles()
    {
        // redirect to user outings list if connected
        if($this->getUser()->isConnected())
        {
            $user_id = $this->getUser()->getId();
            $this->redirect('articles/list/users/'.$user_id);
        }
        else
        {
            sfLoader::loadHelpers('Url');
            $this->redirect(url_for('@login', true).'?redirect=articles/myarticles');
        }
    }
}
