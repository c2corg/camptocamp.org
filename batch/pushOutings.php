<?php
/**
 * Batch that pushes the latest outings created on the site in the last hour to the metaengine
 *
 * @version $Id: pushOutings.php 2504 2007-12-11 16:11:50Z alex $
 */

define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/..'));
define('SF_APP',         'frontend');
define('SF_ENVIRONMENT', 'prod');
define('SF_DEBUG',       false);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

// needed for doctrine connection to work
$context = sfContext::getInstance();

// for rating summing-up :
sfLoader::loadHelpers(array('Field', 'I18N', 'General'));

// we have to do this, else we get a PHP Fatal error:  
// Call to a member function formatExists() on a non-object in /usr/share/php/symfony/i18n/sfI18N.class.php on line 132
// cf. http://www.symfony-project.com/forum/index.php/m/32891/
$dir = sfConfig::get('sf_app_i18n_dir');
$i18n = $context->getI18N();
$i18n->setMessageSourceDir($dir, 'fr');

$n = 0;
$meta_url = sfConfig::get('app_meta_engine_base_url');
$user_id = sfConfig::get('app_meta_engine_c2c_id'); // camptocamp.org metaengine id
$user_key = sfConfig::get('app_meta_engine_c2c_key'); // camptocamp.org key for push
$meta_activities = sfConfig::get('app_meta_engine_activities');

$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><outings xmlns="http://meta.camptocamp.org" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://meta.camptocamp.org/metaengineschema.xsd"></outings>');

// get (id, lang) of outings created in the last hour:
$latest_outings = Outing::listRecentInTime(900);

// fetch info for those (id, lang) : 
foreach ($latest_outings as $outing)
{
    $id = $outing['document_id'];
    $lang = $outing['culture'];
    //echo "Fetching data and building XML for outing ($id, $lang) ... \n";
    $object = Document::find('Outing', $id);
    if (!$object) continue;
    
    $n++;
    $item = $xml->addChild('outing');
    
    $object->setCulture($lang);
    $item->addChild('name', htmlspecialchars($object->get('name')));
    
    if (($lon = $object->get('lon')) && ($lat = $object->get('lat')) && ($ele = $object->get('elevation')))
    {
        // this is the case when a GPX has been uploaded on the outing.
        $item->addChild('geom', "$lon,$lat");
        $item->addChild('elevation', $ele);
    }
    elseif ($associated_routes = Association::findAllAssociatedDocs($id, $fields = array('id', 'lon', 'lat', 'elevation'), 'ro'))
    {
        // find highest route
        $max_ele = 0;
        foreach ($associated_routes as $route)
        {
            if (($ele = $route['elevation']) && ($ele > $max_ele) && ($lon = $route['lon']) && ($lat = $route['lat']))
            {
                $highest_route = $route;
                $max_ele = $ele;
            }
        }
        
        // get route facing and rating if there is just one associated route:
        // fetch additional fields :
        if (count($associated_routes) == 1)
        {
            $single_route = Document::find('Route', $route['id'], array('global_rating', 'facing', 'engagement_rating', 'equipment_rating', 'toponeige_technical_rating', 'toponeige_exposition_rating', 'labande_ski_rating', 'labande_global_rating', 'ice_rating', 'mixed_rating', 'rock_free_rating', 'rock_required_rating', 'aid_rating', 'hiking_rating', 'snowshoeing_rating'));
            if ($rating = trim(field_route_ratings_data($single_route, false, false, true))) 
                $item->addChild('rating', $rating);
            if ($facing = $single_route['facing']) 
                $item->addChild('facing', $facing);
        }
        
        if ($max_ele > 0)
        {
            $item->addChild('geom', "$lon,$lat");
            $item->addChild('elevation', $max_ele);
        }
        else
        {
            // could not find any geolocalized route to link
            // => we go to the next step : find highest associated summits to these routes
            $coords = Route::findHighestAssociatedSummitCoords($associated_routes);
            if ($coords['ele'] > 0)
            {
                if (strlen($coords['lon']) && strlen($coords['lat'])) 
                {
                    $item->addChild('geom', $coords['lon'] . ',' . $coords['lat']);
                }
                $item->addChild('elevation', $coords['ele']);
            }
        }
    }
    elseif ($associated_sites = Association::findAllAssociatedDocs($id, $fields = array('id', 'lon', 'lat', 'elevation'), 'to') ) 
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
        }
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
    $item->addChild('url', "http://www.camptocamp.org/outings/$id/$lang/" . make_slug(htmlspecialchars($object->get('name'))));
}

if (!$n)
{
    exit; // it's better to keep the script mute if not necessary, thus avoiding cron to send useless mails
}

// debug info :
//echo "XML created : \n" . $xml->asXML() . " \n";

$b = new sfWebBrowser();
try
{
    if (!$b->get($meta_url)->responseIsError()) 
    {
        // Successful response (eg. 200, 201, etc)
        //echo "Pushing $n outing(s) ... \n";
        $b->post($meta_url . 'outings/push', array('metaengine_user_id' => $user_id, 
                                                   'metaengine_user_key' => $user_key, 
                                                   'metaengine_xml' => urlencode($xml->asXML())));
        
        $response = $b->getResponseXml();
        
        if ($response->status == 1)
        {
            //echo "Push succeeded. \n";
        }
        else
        {
            // now, what append ???
            // try to get more info on what could make it fail
            //var_dump($xml->asXML());
            //var_dump($response);

            foreach ($response->errors->error as $error)
            {
                if (!isset($complaint)) {
                    echo "Push failed for outings:\n";
                    $complaint = 1;
                }
                echo "- $error->outing_id : $error->error_message\n";
            }
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
    die('Adapter error: ' . $e->getMessage() . "\n");
}

