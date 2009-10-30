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
            // here, we add the summit name to route names :
            $associated_routes = array_filter($this->associated_docs, array('c2cTools', 'is_route'));
            $associated_routes = Route::addBestSummitName($associated_routes, $this->__(' :').' ');
            $associated_docs = array_filter($this->associated_docs, array('c2cTools', 'is_not_route'));
            $associated_docs = array_filter($associated_docs, array('c2cTools', 'is_not_image'));
            $associated_docs = array_merge($associated_docs, $associated_routes);
    
            // sort by document type, name
            if (count($associated_docs))
            {
                foreach ($associated_docs as $key => $row)
                {
                    $module[$key] = $row['module'];
                    $name[$key] = search_name($row['name']);
                }
                array_multisort($module, SORT_STRING, $name, SORT_STRING, $associated_docs);
            }
            $this->associated_users = array_filter($associated_docs, array('c2cTools', 'is_user'));
            $this->associated_documents = $associated_docs;
    
            sfLoader::loadHelpers(array('sfBBCode', 'SmartFormat'));
            $abstract = strip_tags(parse_links(parse_bbcode_abstract($this->document->get('abstract'))));
            $this->getResponse()->addMeta('description', $abstract);
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

    public function executeFilter()
    {   
        $this->setPageTitle($this->__('Search a ' . $this->getModuleName()));
        $this->setTemplate('../../documents/templates/filter');
    } 
        
    protected function getSortField($orderby)
    {
        switch ($orderby)
        {
            case 'cnam': return 'mi.search_name';
            case 'act':  return 'm.activities';
            case 'ccat':  return 'm.categories';
            case 'ctyp': return 'm.article_type';
            default: return NULL;
        }
    }    

    protected function getListCriteria()
    {
        $conditions = $values = array();

        // article criteria
        $this->buildCondition($conditions, $values, 'String', 'mi.search_name', array('cnam', 'name'));
        $this->buildCondition($conditions, $values, 'Multi', 'categories', 'ccat');
        $this->buildCondition($conditions, $values, 'Item', 'm.article_type', 'ctyp');
        $this->buildCondition($conditions, $values, 'Array', 'activities', 'act');
        $this->buildCondition($conditions, $values, 'List', 'm.id', 'id');

        // user criteria
        $this->buildCondition($conditions, $values, 'Multilist', array('u', 'main_id'), 'user', 'join_user_id');
        $this->buildCondition($conditions, $values, 'Multilist', array('u', 'main_id'), 'users', 'join_user_id');

        if (!empty($conditions))
        {
            return array($conditions, $values);
        }

        return array();
    }

    protected function filterSearchParameters()
    {
        $out = array();

        $this->addNameParam($out, 'cnam');
        $this->addListParam($out, 'ccat');
        $this->addListParam($out, 'act');
        $this->addParam($out, 'ctyp');

        return $out;
    }
}
