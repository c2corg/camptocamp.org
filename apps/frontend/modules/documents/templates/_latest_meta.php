<div id="on_the_web" class="latest">
<?php
use_helper('SmartDate', 'JavascriptQueue');

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
$cookie_position = array_search('on_the_web', sfConfig::get('app_personalization_cookie_fold_positions'));

echo javascript_queue('
var section_list = $("#on_the_web_section_list");

function load() {
  $.get("' . $sf_data->getRaw('feed_url') . '").done(function(data) {
    var $xml = $(data), date, count = 0;

    $xml.find("item").each(function() {
      var $this = $(this), item = {}, els;
      $.each(["title", "link", "description", "pubDate", "author"], function(i, v) {
        item[v] = $this.find(v).text();
      });

      if (!count) section_list.html("");
      count++;
      if (count > 10) return;

      if (date != item.pubDate) {
        els = item.pubDate.split("-");
        datespan =  $("<span/>", { "class": "date",  text: els[2] + "/" + els[1] });
      } else {
        datespan = "";
      }
      date = item.pubDate;
      lang = item.description.split(" , ")[5];
      author = item.author.substring(item.author.indexOf("(") - 1);

      section_list.append($("<li/>", { "class": (count % 2) ? "even" : "odd" })
        .append(datespan, $("<a/>", { href: item.link, hreflang: lang, text: item.title }),
          $("<span/>", { "class": "meta", text: author })));
    });
  }).fail(function() {
    section_list.html("' . __('No recent changes available') . '");
  });
}

if (!C2C.shouldHide('. $cookie_position . ', true)) {
  load();
} else {
  $("#on_the_web_toggle").one("click", load);
}
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
echo javascript_tag('C2C.setSectionStatus(\'on_the_web\', '.$cookie_position.', '.((!$default_open) ? 'false' : 'true').");");
?>
</div>
