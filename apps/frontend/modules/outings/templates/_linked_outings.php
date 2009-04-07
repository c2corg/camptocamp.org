<?php
echo '<p style="margin-top:5px;">' .
     link_to(__('List all linked outings'), "outings/list?$module=$id&orderby=date&order=desc") .
     ' - ' .
     image_tag(sfConfig::get('app_static_url') . '/static/images/picto/rss.png',
               array('alt'=> 'RSS', 'title'=>__('RSS list'))) . ' ' .
     link_to(__('RSS list'), "outings/rss?$module=$id&orderby=date&order=desc") .
     '</p>';
