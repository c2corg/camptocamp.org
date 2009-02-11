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
        
        if (!$this->document->isArchive())
        {
            $this->associated_routes = Route::getAssociatedRoutesData($this->associated_docs);
            $this->associated_huts = Hut::getAssociatedHutsData($this->associated_docs);
    
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
            case 'anam': return 'ai.name';
            case 'geom': return 'm.geom_wkt';
            default: return NULL;
        }
    } 

    protected function getListCriteria()
    {   
        $conditions = $values = array();

        if ($areas = $this->getRequestParameter('areas'))
        {
            Document::buildListCondition($conditions, $values, 'ai.id', $areas);
        }

        if ($pname = $this->getRequestParameter('pnam', $this->getRequestParameter('name')))
        {
            $conditions[] = 'mi.search_name LIKE remove_accents(?)';
            $values[] = '%' . urldecode($pname) . '%';
        }

        if ($palt = $this->getRequestParameter('palt'))
        {
            Document::buildCompareCondition($conditions, $values, 'm.elevation', $palt);
        }

        if ($tp = $this->getRequestParameter('tp'))
        {
            $conditions[] = 'm.public_transportation_rating = 1';
        }

        if ($geom = $this->getRequestParameter('geom'))
        {
            Document::buildGeorefCondition($conditions, $geom);
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
        $this->addNameParam($out, 'pnam');
        $this->addCompareParam($out, 'palt');
        $this->addParam($out, 'tp');
        $this->addParam($out, 'geom');
        
        return $out;
    }
}
