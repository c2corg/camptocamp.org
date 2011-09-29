<?php
/**
 * Batch that pushes the latest outings created on the site in the last hour to the metaengine
 *
 * @version $Id: pushOutings.php 2330 2007-11-13 09:01:53Z alex $
 */

define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/..'));
define('SF_APP',         'frontend');
define('SF_ENVIRONMENT', 'prod');
define('SF_DEBUG',       false);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

// needed for doctrine connection to work
sfContext::getInstance();

class totopush extends Document
{
    public static function listRecentInTime2($module, $mean_time)
    {    
        $sql = 'SELECT d.document_id, d.culture FROM app_documents_versions d ' .
                'LEFT JOIN app_documents_archives a ON d.document_id = a.id WHERE a.id = 106987 GROUP BY d.document_id, d.culture';
        return sfDoctrine::connection()->standaloneQuery($sql)->fetchAll();
    } 
}

$n = 0;
$meta_url = sfConfig::get('app_meta_engine_base_url');
$user_id = sfConfig::get('app_meta_engine_c2c_id'); // camptocamp.org metaengine id
$user_key = sfConfig::get('app_meta_engine_c2c_key'); // camptocamp.org key for push
$meta_activities = sfConfig::get('app_meta_engine_activities');

$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><outings xmlns="http://meta.camptocamp.org" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://meta.camptocamp.org/metaengineschema.xsd"></outings>');

// get (id, lang) of outings created in the last hour:
$latest_outings = totopush::listRecentInTime2('outings', 3600);

// fetch info for those (id, lang) : 
foreach ($latest_outings as $outing)
{
    $n++;
    $item = $xml->addChild('outing');
    $id = $outing['document_id'];
    $lang = $outing['culture'];
    echo "Fetching data and building XML for outing ($id, $lang) ... \n";
    $object = Document::find('Outing', $id);
    $object->setCulture($lang);
    $item->addChild('name', $object->get('name'));
    
    if (($lon = $object->get('lon')) && ($lat = $object->get('lat')) && ($ele = $object->get('elevation')))
    {
        // this is the case when a GPX has been uploaded on the outing.
        $item->addChild('geom', "$lon,$lat");
        $item->addChild('elevation', $ele);
        //echo "Outing lon lat and ele are sent\n";
    }
    elseif( $associated_sites = Association::findAllAssociatedDocs($id, $fields = array('id', 'lon', 'lat', 'elevation'), 'to') ) 
    {
        // find highest site
        $max_ele = 0;
        foreach ($associated_sites as $site)
        {
            if (($ele = $site['elevation']) && ($ele > $max_ele) && ($lon = $site['lon']) && ($lat = $site['lat']))
            {
                $highest_site = $site;
                $max_ele = $site['elevation'];
            }
        }
        if ($max_ele > 0)
        {
            $item->addChild('geom', "$lon,$lat");
            $item->addChild('elevation', $max_ele);
            //echo "Highest site lon lat and ele are sent\n";
        }
        else
        {
            // could not find any geolocalized document to link
            $item->addChild('region_name', ' ');
            //echo "No site lon lat and ele are found \n";
        }
    }
    elseif($associated_routes = Association::findAllAssociatedDocs($id, $fields = array('id', 'lon', 'lat', 'elevation'), 'ro'))
    {
        // find highest route
        $max_ele = 0;
        foreach ($associated_routes as $route)
        {
            if (($ele = $route['elevation']) && ($ele > $max_ele) && ($lon = $route['lon']) && ($lat = $route['lat']))
            {
                $highest_route = $route;
                $max_ele = $route['elevation'];
            }
        }
        if ($max_ele > 0)
        {
            $item->addChild('geom', "$lon,$lat");
            $item->addChild('elevation', $max_ele);
            //echo "Highest route lon lat and ele are sent\n";
        
            // get route facing and rating if they are set:
            // fetch additional fields :
            $highest_route = Document::find('Route', $highest_route['id'], array('global_rating', 'facing'));
            if ($rating = $highest_route['global_rating']) $item->addChild('rating', $rating);
            if ($facing = $highest_route['facing']) $item->addChild('facing', $facing); 
        }
        else
        {
            // could not find any geolocalized route to link
            // => we go to the next step : find highest associated summits to these routes
            $coords = Route::findHighestAssociatedSummitCoords($associated_routes);
            if ($coords['ele'] > 0)
            {
                $item->addChild('geom', $coords['lon'] . ',' . $coords['lat']);
                $item->addChild('elevation', $coords['ele']);
                //echo "Highest summit lon lat and ele are sent\n";
            }
            else
            {
                $item->addChild('region_name', ' '); 
                //echo "No summit lon lat and ele are found \n";
            }
        }
    }
    else
    {
        // could not find any geolocalized document to link
        $item->addChild('region_name', ' '); // because metaengine needs a geom or a region
        //echo "No associated doc found \n";
    }
    
    $item->addChild('date', $object->get('date'));
    $item->addChild('lang', $lang);
    $activities = $object->get('activities');
    foreach ($activities as $activity)
    {
        if (array_key_exists($activity, $meta_activities))
        {
            $item->addChild('activity', $meta_activities[$activity]);
        }
    }
    
    $item->addChild('original_outing_id', $id); 
    $item->addChild('url', "http://www.camptocamp.org/outings/$id/$lang"); 
}
die($xml->asXML() . " \n");
if (!$n)
{
    exit; // it's better to keep the script mute if not necessary, thus avoiding cron to send useless mails
    //die("No outing to send.\n");
    //echo "XML created : \n" . $xml->asXML() . " \n";
}
/*
$b = new sfWebBrowser();
try
{
    if (!$b->get($meta_url)->responseIsError()) 
    {
        // Successful response (eg. 200, 201, etc)
        echo "Pushing $n outing(s) ... \n";
        $b->post($meta_url . 'outings/push', array('metaengine_user_id' => $user_id, 
                                                   'metaengine_user_key' => $user_key, 
                                                   'metaengine_xml' => urlencode($xml->asXML())));

        $response = $b->getResponseXml();
        
        if ($response->status == 1)
        {
            echo "Push succeeded. \n";
        }
        else
        {
            echo 'Push failed with error message: "'. $response->errmsg ."\" \n";
        }
    }
    else
    {
        // Error response (eg. 404, 500, etc)
        die("There was an error contacting the MetaEngine Server \n");
    }
}
catch (Exception $e)
{
    // Adapter error (eg. Host not found)
    die("Adapter error \n");
}
*/

