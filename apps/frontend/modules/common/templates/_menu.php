<?php
use_helper('Forum','Button', 'ModalBox', 'General');

?>
<div id="menu_up">
    <?php
    $perso = c2cPersonalization::getInstance();
    $act_filter = $perso->getActivitiesFilter();
    $main_filter_switch_on = $perso->isMainFilterSwitchOn();
    $alist = sfConfig::get('app_activities_list');
    array_shift($alist); // remove the 0 entry
    $light = array_fill(0, count($alist), '');
    $activities_class = array();

    if ($main_filter_switch_on && count($act_filter))
    {
        // we don't use array_diff, since array keys are shifted
        $unselected_act = array();
        foreach ($alist as $k => $act)
        {
            if (!in_array($k+1, $act_filter)) $unselected_act[] = $k;
        }

        foreach ($unselected_act as $act_id)
        {
            $light[$act_id] = '_light';
        }
        foreach ($act_filter as $act_id)
        {
            $activities_class[] = 'act' . $act_id;
        }
    }
    ?>
    <div id="quick_switch<?php echo empty($activities_class) ? '' : '" class="' . implode(' ', $activities_class) ?>">
        <?php
        foreach ($alist as $act_id => $activity)
        {
            $alt = ($act_filter == array($act_id)) 
                   ? __('switch_off_activity_personalisation')
                   : __('switch_to_' . $alist[$act_id]) ;
            $image_tag = picto_tag('activity_' . ($act_id+1) . $light[$act_id], $alt);
            echo link_to($image_tag, '@quick_activity?activity=' . ($act_id+1), array('class' => 'qck_sw'));
        }
        ?>
    </div>
    <?php
    include_partial('common/menu_content',
                    array('sf_cache_key' => ($is_connected ? 'connected' : 'not_connected') . '_' . $lang,
                          'lang' => $lang,
                          'is_connected' => $is_connected));
    ?>
</div>

