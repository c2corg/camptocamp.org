<?php
use_helper('AutoComplete', 'Field', 'General');

$is_connected = $sf_user->isConnected();
$is_moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
$is_not_archive = !$document->isArchive();
$is_not_merged = !$document->get('redirects_to');
$show_link_to_delete = ($is_not_archive && $is_not_merged && $is_moderator);
$show_link_tool = ($is_not_archive && $is_not_merged && $is_connected);
if (!isset($show_link_to_delete))
{
    $show_link_to_delete = false;
}

// We'll need the id of this html objerct to update it through ajax
$id_no_associated_docs = "no_associated_docs";
// We'll need the id of this html objerct to update it through ajax
$id_list_associated_docs = "list_associated_docs";

if (!count($associated_documents))
{
    echo '<p class="default_text"  id="', $id_no_associated_docs.'">', __('No associated document found'), '</p>';
}

echo '<ul id="'.$id_list_associated_docs.'">';
if (count($associated_documents))
{
    foreach ($associated_documents as $doc)
    {
        $doc_id = $doc->get('id');
        $module = $doc['module'];
        $type = c2cTools::Module2Letter($module) . 'c';
        $idstring = $type . '_' . $doc_id;

        echo '<li id="'.$idstring.'">';

        echo picto_tag('picto_' . $module, __($module));
        echo ' ' . link_to($doc['name'], "@document_by_id_lang_slug?module=$module&id=" . $doc['id'] .
                                         '&lang=' . $doc['culture'] . '&slug=' . make_slug($doc['name']));
        if ($show_link_to_delete)
        {
            $strict = ($type == 'cc') ? 0 : 1;
            echo c2c_link_to_delete_element($type, $doc_id, $document->get('id'), false, $strict);
        }

        echo '</li>';

    }
}
echo '</ul>';
if ($show_link_tool)
{
?>
    <div id="association_tool" class="plus">
    <p><?php echo __('You can associate this article with any existing document using the following tool:'); ?></p>
<?php
    $modules_list = array('articles', 'summits', 'sites', 'routes', 'huts', 'parkings', 'products', 'outings', 'books');
    if ($document->get('article_type') == 2) // only personal articles need user association
    {
        $modules_list[] = 'users';
    }

    echo c2c_form_add_multi_module('articles',
        $document->get('id'), $modules_list, 11, 
        $id_list_associated_docs, false, 'indicator', $id_no_associated_docs);

    if (!$is_moderator && $is_connected && ($document->get('article_type') == 2))
    {
        echo javascript_tag("if (!user_is_author) { $('doc_add').hide(); $('ac_form').hide(); }");
    }
?>
    </div>
<?php
}


