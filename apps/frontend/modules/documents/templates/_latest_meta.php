<div id="on_the_web" class="latest">
<?php
use_helper('SmartDate');

$response = sfContext::getInstance()->getResponse();

if (!isset($default_open))
{
    $default_open = true;
}
$tr_module =  __('meta outings');
include_partial('documents/home_section_title',
                array('module'            => 'on_the_web',
                      'custom_section_id' => 'on_the_web',
                      'custom_title'      => link_to(__('Latest outings from MetaEngine'),
                                                     sfConfig::get('app_meta_engine_base_url')),
                      'custom_rss'        => link_to('',
                                                     sfConfig::get('app_meta_engine_base_url') . 'outings',
                                                     array('class' => 'home_title_right picto_rss',
                                                           'title' => __('Subscribe to latest outings from MetaEngine'))),
                      'custom_title_icon' => 'outings'));
?>
<?php
echo javascript_queue('
$.get("' . html_entity_decode(html_entity_decode($feed_url)) . '").done(function(data) {
  var $xml = $(data);
  var count = 0;
  var date;

  $xml.find("item").each(function() {
    var $this = $(this),
      item = {
        title: $this.find("title").text(),
        link: $this.find("link").text(),
        description: $this.find("description").text(),
        pubDate: $this.find("pubDate").text(),
        author: $this.find("author").text()
      }

    if (count == 0) {
      $("#on_the_web_section_list").html("");
    }
    count++;
    if (count > 10) { return; }
    li_class = (count % 2) ? "even" : "odd";

    if (date != item["pubDate"]) {
      elems = item["pubDate"].split("-");
      datespan = "<span class=\"date\">" + elems[2] + "/" + elems[1] + "</span>";
    } else {
      datespan = "";
    }

    date = item["pubDate"];
    lang = item["description"].split(" , ")[5];
    author = item["author"].substring(item["author"].indexOf("(") - 1);

    $("#on_the_web_section_list").append(
      "<li class=\"" + li_class + "\">"
      + datespan
      + "<a href=\"" + item["link"] + "\" "
      + "hreflang=\"" + lang + "\""
      + ">" + item["title"] + "</a> "
      + "<span class=\"meta\">" + author + "</span>"
      + "</li>"
    );
  });
}).fail(function() {
  $("#on_the_web_section_list").html("' . __('No recent changes available') . '");
});
');
?>
<div id="on_the_web_section_container" class="home_container_text">
<ul id="on_the_web_section_list" class="dated_changes">
<li><?php echo image_tag(sfConfig::get('app_static_url') . '/static/images/indicator.gif') . __(' loading...'); ?></li>
</ul>
<div class="home_link_list">
<?php echo link_to('meta.camptocamp.org', sfConfig::get('app_meta_engine_base_url')) ?>
</div>
</div>
<?php
$cookie_position = array_search('on_the_web', sfConfig::get('app_personalization_cookie_fold_positions'));
echo javascript_tag('C2C.setSectionStatus(\'on_the_web\', '.$cookie_position.', '.((!$default_open) ? 'false' : 'true').");");
?>
</div>
