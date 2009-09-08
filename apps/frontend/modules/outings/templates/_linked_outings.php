<?php
use_helper('General');

if(isset($nb_outings))
{
    $nb_outings = " ($nb_outings)";
}
else
{
    $nb_outings = '';
}
echo '<p class="list_link">' .
     picto_tag('action_list') . ' ' .
     link_to(__('List all linked outings') . $nb_outings, "outings/list?$module=$id&orderby=date&order=desc") .
     ' - ' .
     link_to(__('recent conditions'), "outings/conditions?$module=$id&date=3W&orderby=date&order=desc") .
     ' - ' .
     picto_tag('picto_rss') . ' ' .
     link_to(__('RSS list'), "outings/rss?$module=$id&orderby=date&order=desc") .
     '</p>';
