<?php
use_helper('Sections', 'Form', 'MyForm');

use_javascript('/static/js/cartoweb/lib/SearchForm', 'last');
use_javascript('/static/js/search', 'last');
use_javascript('/static/js/fold', 'last');

$module = $sf_context->getModuleName();
?>

<div class="clearing">
    <span class="article_title img_title_<?php echo $module ?>"><?php echo __($module . ' list') ?></span>
</div>

<div id="nav_space">&nbsp;</div>
<div id="nav_tools">
    <div id="nav_tools_top"></div>
    <div id="nav_tools_content">
        <ul>
            <li><?php echo link_to(__('Back to list'),
                        "@default_index?module=$module",
                        array('class' => 'action_back nav_edit')) ?>
            </li>
        </ul>
    </div>
    <div id="nav_tools_down"></div>
</div>

<div id="wrapper_context">
<div id="ombre_haut">
    <div id="ombre_haut_corner_right"></div>
    <div id="ombre_haut_corner_left"></div>
</div>

<div id="content_article">
<div id="article">

<!-- search -->
<form id="search_form" name="search_form" action="">
    <?php
    echo start_section_tag('Search criteria', 'search_form');
    echo group_tag('Name:', 'name', 'input_tag', null, array('class' => 'long_input'));
    include_partial('search_form');
    echo submit_tag(__('Search'), array('onclick' => 'do_search(this.form); return false;', 'class'=>'srch_submit'));
    echo end_section_tag();
    ?>
</form>

<!-- map -->
<?php
/* We don't want the map for the book search page. Yet, we need to include
 * the Map helper for the activate_search javascript call to succeed.
 */
use_helper('Map');
$search_url = url_for("@query?module=$module");
if ($module != 'books') // Grrr...
{
    $container_div = 'map_container';
    echo start_section_tag('Interactive map', $container_div, 'closed', true);
    include_partial('documents/maps', array(
        'search'            => true,
        'search_url'        => $search_url,
        'displayed_layers'  => array($module),
        'container_div'     => $container_div,
        'tip_close'         => __('section close')
    ));
    echo end_section_tag(true);
}
?>

<!-- search results -->
<?php echo start_section_tag('Search results', 'search_container') ?>
<div id="search_results" /></div>
<?php echo end_section_tag(); ?>

<script type="text/javascript">
    if (window.addEventListener) {
        window.addEventListener('load', activate_search('search_form', 'search_results', '<?php echo $search_url ?>'), false);
    } else if (window.attachEvent) {
        window.attachEvent('onload', activate_search('search_form', 'search_results', '<?php echo $search_url ?>'));
    }
</script>
</div>
</div>

<?php include_partial('common/content_bottom') ?>
