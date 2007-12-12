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
     * Additional fields to display in documents lists (additional, relative to id, culture, name)
     * if field comes from i18n table, prefix with 'mi.', else with 'm.' 
     */  
    protected $fields_in_lists = array('m.categories', 'm.activities', 'm.article_type');
    
    /**
     * Executes view action.
     */
    public function executeView()
    {
        parent::executeView();
        
        // here, we add the summit name to route names :
        $associated_routes = array_filter($this->associated_docs, array('c2cTools', 'is_route'));
        $associated_routes = Route::addBestSummitName($associated_routes);
        $this->associated_docs = array_filter($this->associated_docs, array('c2cTools', 'is_not_route'));
        $this->associated_docs = array_filter($this->associated_docs, array('c2cTools', 'is_not_image'));
        $this->associated_docs = array_merge($this->associated_docs, $associated_routes);
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
        // we know here that document $id exists and that its model is the current one (Article).
        $article = Document::find('Article', $id, array('article_type', 'is_protected'));
        
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

    /**
     * Executes "associate article with document" action
     * associated document can only be : articles, summits, books, huts, outings, routes, sites, users
     * ... restricted in security.yml to logged people
     */
    public function executeAddassociation()
    {
        $article_id = $this->getRequestParameter('article_id');
        $document_id = $this->getRequestParameter('document_id');
        
        // association cannot be with self.
        if ($document_id == $article_id )
        {
            return $this->ajax_feedback('Operation not allowed');
        }

        $document = Document::find('Document', $document_id, array('id', 'module', 'is_protected'));
        $module = $document->get('module');

        $user = $this->getUser();
        $user_id = $user->getId();

        if (!$document)
        {
            return $this->ajax_feedback('Document does not exist');
        }

        $article = Document::find('Article', $article_id, array('id', 'is_protected', 'article_type'));

        if (!$article)
        {
            return $this->ajax_feedback('Article does not exist');
        }
        
        if ($article->get('is_protected'))
        {
            return $this->ajax_feedback('Article is protected');
        }

        switch ($module)
        {
            case 'articles':
                $type = 'cc';
                break;
            case 'summits':
                $type = 'sc';
                break;
            case 'books':
                $type = 'bc';
                break;
            case 'huts':
                $type = 'hc';
                break;
            case 'outings':
                $type = 'oc';
                break;
            case 'routes':
                $type = 'rc';
                $summit = explode(' [',$this->getRequestParameter('summits_name'));
                break;
            case 'sites':
                $type = 'tc';
                break;
            case 'users':
                $type = 'uc';
                break;
            default:
                return $this->ajax_feedback('Wrong association type');
                break;
        }
        
        if (($article->get('article_type') == 2) && ($type == 'uc')) // only personal articles (type 2) need user association
        {
            return $this->ajax_feedback('Could not perform association');
        }

        $a = new Association;
        $status = $a->doSaveWithValues($document_id, $article_id, $type, $user_id);

        if (!$status)
        {
            return $this->ajax_feedback('Could not perform association');
        }

        
        $module_name = $this->__($module);

        // cache clearing for current doc in every lang:
        $this->clearCache('articles', $article_id, false, 'view');
        $this->clearCache($module, $document_id, false, 'view');

        sfLoader::loadHelpers(array('Tag', 'Url', 'Asset'));

        $document->setBestName($user->getPreferedLanguageList());
        
        $bestname = ($module == 'routes') ? $summit[0] . ' : ' . $document->get('name') : $document->get('name');

        $out = '<li>'. image_tag('/static/images/modules/' . $module . '_mini.png', 
                                    array('alt' => $module_name, 'title' => $module_name)) . 
               ' ' . link_to($bestname, "@document_by_id?module=$module&id=$document_id") . '</li>';

        return $this->renderText($out);
    }
        
    
}
