<?php
/**
 * books module actions.
 *
 * @package    c2corg
 * @subpackage books
 * @version    $Id: actions.class.php 2539 2007-12-20 16:58:23Z alex $
 */
class booksActions extends documentsActions
{
    /**
     * Model class name.
     */
    protected $model_class = 'Book';
 
    /**
     * Executes view action.
     */
    public function executeView()
    {
        parent::executeView();

        if (!$this->document->isArchive() && $this->document['redirects_to'] == NULL)
        {
            $user = $this->getUser();
            $prefered_cultures = $user->getCulturesForDocuments();
            
            $this->associated_summits = Summit::getAssociatedSummitsData($this->associated_docs);
            $this->associated_routes = Route::getAssociatedRoutesData($this->associated_docs, $this->__(' :').' ');
            $this->associated_huts = Hut::getAssociatedHutsData($this->associated_docs);
            $this->associated_sites = Site::getAssociatedSitesData($this->associated_docs);
            
            // add linked docs areas
            $parent_ids = array();
            $associated_areas = array();
            if (count($this->associated_docs))
            {
                foreach ($this->associated_docs as $doc)
                {
                    $parent_ids[] = $doc['id'];
                }
                $associated_areas = GeoAssociation::findWithBestName($parent_ids, $prefered_cultures, array('dr', 'dd', 'dc'));
            }
            $associated_areas = array_merge($this->associated_areas, $associated_areas);
            $this->associated_areas = Area::getAssociatedAreasData($associated_areas);
            
            $cas = count($this->associated_summits);
            $car = count($this->associated_routes);
            $cah = count($this->associated_huts);
            $cab = count($this->associated_sites);
    
            $this->section_list = array('summits' => ($cas != 0),
                                        'routes' => ($car != 0),
                                        'huts' => ($cah != 0),
                                        'sites' => ($cab != 0),
                                        'docs' => ($cas + $car + $cah +$cab == 0));
    
            $description = array($this->__('book') . ' :: ' . $this->document->get('name'),
                                 $this->getActivitiesList());
            $this->getResponse()->addMeta('description', implode(' - ', $description));
        }
    }

    protected function getSortField($orderby)
    {
        switch ($orderby)
        {
            case 'bnam': return 'mi.search_name';
            case 'act':  return 'm.activities';
            case 'auth': return 'm.author';
            case 'edit': return 'm.editor';
            case 'btyp': return 'm.book_type';
            case 'lang': return 'm.langs';
            default: return NULL;
        }
    }

    protected function getListCriteria()
    {
        $params_list = c2cTools::getAllRequestParameters();
        
        return Book::buildListCriteria($params_list);
    }

    protected function filterSearchParameters()
    {
        $out = array();

        $this->addListParam($out, 'areas');
        $this->addNameParam($out, 'bnam');
        $this->addListParam($out, 'btyp');
        $this->addListParam($out, 'lang');
        $this->addListParam($out, 'act');
        $this->addNameParam($out, 'auth');
        $this->addNameParam($out, 'edit');
        $this->addParam($out, 'bcult');

        return $out;
    }
}
