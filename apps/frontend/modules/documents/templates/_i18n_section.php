<?php
use_helper('Language');
$module = $sf_context->getModuleName();

if (!$document->isArchive()): ?>
    <div class="switch_lang">
        <div class="article_infos_titre">
        <?php
        echo (c2cTools::mobileVersion() ? __('Switch lang short:') : __('Switch lang:')) . '</div>';
        echo language_select_list($module, $document->get('id'), $document->getCulture(),
                                  $sf_data->getRaw('languages')); // instead of $languages: XSS protection deactivation
        ?>
    </div>
<?php endif ?>

<?php if ($document->isAvailable()): ?>
    <div class="article_contenu">
        <?php
        $i18n_args = array('document' => $document, 
                           'needs_translation' => isset($needs_translation) ? $needs_translation : false,
                           'images' => isset($images) ? $images : null);
        if (isset($filter_image_type))
        {
            $i18n_args['filter_image_type'] = $filter_image_type;
        }
        if (isset($associated_books))
        {
            $i18n_args['associated_books'] = $associated_books;
            $i18n_args['main_id'] = $document->get('id');
        }
        if (isset($associated_areas))
        {
            $i18n_args['associated_areas'] = $associated_areas;
        }
        if (!isset($ids))
        {
            $ids = $document->get('id');
        }
        $i18n_args['ids'] = $ids;
        
        include_partial('i18n', $i18n_args);
        
        if (isset($needs_translation) && $needs_translation && !c2cTools::mobileVersion())
        {
            echo javascript_tag('Event.observe(window, \'load\', function() {'.
"GoogleTranslation.i18n=['".__('translate')."','".__('untranslate')."','".__(' loading...')."'];".
"GoogleTranslation.language_from='".$document->getCulture()."';".
"GoogleTranslation.language_to='".__('meta_language')."';".
'GoogleTranslation.base_url = "https://www.googleapis.com/language/translate/v2?key='.
sfConfig::get('app_google_api_key').'&format=html"; GoogleTranslation.init_buttons();});');
        } ?>
    </div>
    
<?php else: ?>
    <p class="separator">
    <?php
    // do not let google index this page, but let it follow the links
    $response = sfContext::getInstance()->getResponse()->addMeta('robots', 'noindex, follow');
    echo __('This document is not available in %1%',
            array('%1%' => format_language_c2c($document->getCulture()))) . ' ' .
         link_to(__('Create it!'),
                 "@document_edit?module=$module&id=" . $document->get('id') . '&lang=' . $document->getCulture());
    ?>
    </p>
<?php endif;
