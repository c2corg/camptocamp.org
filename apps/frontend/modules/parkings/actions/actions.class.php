<?php
/**
 * parkings module actions.
 *
 * @package    c2corg
 * @subpackage parkings
 * @version    $Id: actions.class.php 1132 2007-08-01 14:38:06Z fvanderbiest $
 */
class parkingsActions extends documentsActions
{
    /**
     * Model class name.
     */
    protected $model_class = 'Parking';

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
            $current_doc_id = $this->getRequestParameter('id');
            
            $main_associated_parkings = c2cTools::sortArray(array_filter($this->associated_docs, array('c2cTools', 'is_parking')), 'elevation');
            
            $parking_ids = array();
            if (count($main_associated_parkings))
            {
                $associated_parkings = Association::addChildWithBestName($main_associated_parkings, $prefered_cultures, 'pp', $current_doc_id);
                $associated_parkings = Parking::getAssociatedParkingsData($associated_parkings);
                
                foreach ($main_associated_parkings as $parking)
                {
                    $parking_ids[] = $parking['id'];
                }
                
                if (count($parking_ids))
                {
                    $associated_parking_routes = Association::findWithBestName($parking_ids, $prefered_cultures, 'pr');
                    $this->associated_docs = array_merge($this->associated_docs, $associated_parking_routes);
                }
            }
            else
            {
                $associated_parkings = $main_associated_parkings;
            }
            
            $this->associated_parkings = $associated_parkings;
            $parking_ids[] = $this->getRequestParameter('id');
            $this->parking_ids = $parking_ids;
            
            $this->associated_routes = Route::getAssociatedRoutesData($this->associated_docs, $this->__(' :').' ');
            $this->associated_huts = c2cTools::sortArray(array_filter($this->associated_docs, array('c2cTools', 'is_hut')), 'elevation');
    
            $description = array($this->__('parking') . ' :: ' . $this->document->get('name'),
                                 $this->getAreasList());
            $this->getResponse()->addMeta('description', implode(' - ', $description));
        }
    }

    public function executeGetdirections()
    {
        sfLoader::loadHelpers(array('GetDirections'));

        $referer = $this->getRequest()->getReferer();
        $dest_id = $this->getRequestParameter('id');
        $service = $this->getRequestParameter('service');
        $user_id = $this->getUser()->getId();
        $lang = $this->getUser()->getCulture();

        // parking coords
        $dest_coords = Document::fetchAdditionalFieldsFor(array(array('id' => $dest_id)), 'Parking', array('lat', 'lon'));

        if (empty($dest_coords) ||
            $dest_coords[0]['lat'] instanceOf Doctrine_Null ||
            $dest_coords[0]['lon'] instanceOf Doctrine_Null)
        {
            return $this->setWarningAndRedirect('Parking does not exists or has no attached geometry', $referer);
        }

        // retrieve best parking name
        if ($service == 'gmaps' || $service == 'livesearch')
        {
            $name = urlencode(DocumentI18n::findBestName($dest_id, $this->getUser()->getCulturesForDocuments(), 'Parking'));
        }

        // user coords
        $user_coords = empty($user_id) ? null : Document::fetchAdditionalFieldsFor(array(array('id' => $user_id)), 'User', array('lat', 'lon'));
 
        if (empty($user_coords) ||
            $user_coords[0]['lat'] instanceOf Doctrine_Null ||
            $user_coords[0]['lon'] instanceOf Doctrine_Null)
        {
            $user_lat = $user_lon = null;
        }
        else
        {
            $user_lat = $user_coords[0]['lat'];
            $user_lon = $user_coords[0]['lon'];
        }

        switch ($service)
        {
            case 'yahoo':
                 $url = yahoo_maps_direction_link($user_lat, $user_lon, $dest_coords[0]['lat'], $dest_coords[0]['lon'], $lang);
                 break;
            case 'livesearch':
                 $url = live_search_maps_direction_link($user_lat, $user_lon, $dest_coords[0]['lat'], $dest_coords[0]['lon'], $name);
                 break;
            case 'gmaps':
            default:
                 $url = gmaps_direction_link($user_lat, $user_lon, $dest_coords[0]['lat'], $dest_coords[0]['lon'], $name, $lang);
                 break;
        }
        $this->redirect($url);
    }

    protected function getSortField($orderby)
    {
        switch ($orderby)
        {
            case 'pnam': return 'mi.name';
            case 'palt': return 'm.elevation';
            case 'tp':  return 'm.public_transportation_rating';
            case 'tpty':  return 'm.public_transportation_types';
            case 'anam': return 'ai.name';
            case 'geom': return 'm.geom_wkt';
            default: return NULL;
        }
    } 

    protected function getListCriteria()
    {   
        $conditions = $values = array();

        $this->buildCondition($conditions, $values, 'Multilist', array('g', 'linked_id'), 'areas', 'join_area');
        $this->buildCondition($conditions, $values, 'String', 'mi.search_name', array('pnam', 'name'));
        $this->buildCondition($conditions, $values, 'Compare', 'm.elevation', 'palt');
        $this->buildCondition($conditions, $values, 'List', 'm.public_transportation_rating', 'tp');
        $this->buildCondition($conditions, $values, 'Array', 'p.public_transportation_types', 'tpty');
        $this->buildCondition($conditions, $values, 'Georef', null, 'geom');

        if (!empty($conditions))
        {
            return array($conditions, $values);
        }

        return array();
    }

    public function executeFilter()
    {
        parent::executeFilter();
    }

    protected function filterSearchParameters()
    {
        $out = array();

        $this->addListParam($out, 'areas');
        $this->addNameParam($out, 'pnam');
        $this->addCompareParam($out, 'palt');
        $this->addListParam($out, 'tp');
        $this->addListParam($out, 'tpty');
        $this->addParam($out, 'geom');
        
        return $out;
    }
}
