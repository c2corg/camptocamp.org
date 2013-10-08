<?php
use_helper('AutoComplete', 'Field', 'General', 'Link');

$id = $document->get('id');
$is_connected = $sf_user->isConnected();
$is_mobile_version = c2cTools::mobileVersion();
$is_moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
$is_not_archive = !$document->isArchive();
$is_not_merged = !$document->get('redirects_to');
if (!isset($show_link_to_delete))
{
    $show_link_to_delete = ($is_not_archive && $is_not_merged && $is_moderator && !$is_mobile_version);
}
if (!isset($show_link_tool))
{
    $show_link_tool = ($is_not_archive && $is_not_merged && $is_connected && !$is_mobile_version);
}
if (!isset($show_default_text))
{
    $show_default_text = true;
}

// We'll need the id of this html objerct to update it through ajax
$id_no_associated_docs = "no_associated_docs";

// We'll need the id of this html objerct to update it through ajax
if (!isset($id_list_associated_docs))
{
    $id_list_associated_docs = "list_associated_docs";
}

if (!count($associated_documents) && $show_default_text)
{
    echo '<p class="default_text" id="', $id_no_associated_docs.'">', __('No associated document found'), '</p>';
}

if (!isset($fixed_type))
{
    $fixed_type = '';
}

if (count($associated_documents))
{
    echo '<ul id="'.$id_list_associated_docs.'">';
    
    foreach ($associated_documents as $doc)
    {
        $doc_id = $doc->get('id');
        $module = $doc['module'];
        if (empty($fixed_type))
        {
            $type = c2cTools::Module2Letter($module) . 'c';
        }
        else
        {
            $type = $fixed_type;
        }
        $idstring = $type . '_' . $doc_id;

        echo '<li id="'.$idstring.'">';

        echo picto_tag('picto_' . $module, __($module));
        echo ' ' . list_link($doc, $module);
        if ($module == 'outings')
        {
            echo ' - ' . field_activities_data($doc, array('raw' => true)) . ' - ' . field_semantic_date_data($doc, 'date');
        }
        if ($show_link_to_delete)
        {
            $strict = ($type == 'cc') ? 0 : 1;
            if (empty($fixed_type))
            {
                echo c2c_link_to_delete_element($type, $doc_id, $id, false, $strict);
            }
            else
            {
                echo c2c_link_to_delete_element($type, $id, $doc_id, true, $strict);
            }
        }

        echo '</li>';

    }
    echo '</ul>';
}
elseif ($show_link_tool)
{
    echo  '<ul id="' . $id_list_associated_docs . '"><li style="display:none"></li></ul>';
}

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
        $id, $modules_list, 11, 
        $id_list_associated_docs, false, 'indicator', $id_no_associated_docs);

    if (!$is_moderator && $is_connected && ($document->get('article_type') == 2))
    {
        echo javascript_tag("if (!document.body.hasAttribute('data-user-author')) {
          document.getElementById('association_tool').style.display = 'none';
        }");
    }
?>
    </div>
<?php
}


