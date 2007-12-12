<?php
/**
 * $Id: components.class.php 1714 2007-09-20 17:40:07Z alex $
 */
 
class commonComponents extends sfComponents
{
    public function executeBanner()
    {
        $this->counter_base_url = sfConfig::get('mod_common_counter_base_url');
        
        $active_banners = sfConfig::get('mod_common_active_banners_values');
        $banners = sfConfig::get('mod_common_banners_list');
        $this->banner = $banners[$active_banners[array_rand($active_banners)]];

        // TODO: filter on languages and activities
    }
}
