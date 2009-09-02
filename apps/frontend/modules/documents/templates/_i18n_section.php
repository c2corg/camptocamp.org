<?php
use_helper('Language');
$module = $sf_context->getModuleName();
?>

    <div class="article_infos_titre_contenu">

    <?php if (!$document->isArchive()): ?>
        <div class="switch_lang">
        <div class="article_infos_titre">
        <?php
        echo __('Switch lang:') . '</div>';
        echo language_select_list($module, $document->get('id'), $document->getCulture(),
                                  $sf_data->getRaw('languages')); // instead of $languages: XSS protection deactivation
        ?>
        </div>
    <?php endif ?>

    <?php if ($document->isAvailable()): ?>
    <div class="article_contenu">
        <?php
        $i18n_args = array('document' => $document, 'needs_translation' => isset($needs_translation) ? $needs_translation : false);
        if (($module == 'routes' || $module == 'sites') && isset($associated_books)) // FIXME is there a nicer way to include the arg?
        {
            $i18n_args['associated_books'] = $associated_books;
            $i18n_args['main_id'] = $document->get('id');
        }
        include_partial('i18n', $i18n_args);
        if (isset($needs_translation) && $needs_translation)
        {
            echo javascript_tag("var translate_params=['".__('translate')."','".__('untranslate')."','".__(' loading...')
                     ."','".$document->getCulture()."','".__('meta_language')."'];");
        }
        ?>
    </div>
    
    <?php else: ?>
    <p class="separator">
    <?php
    echo __('This document is not available in %1%',
            array('%1%' => format_language_c2c($document->getCulture()))) . ' ' .
         link_to(__('Create it!'),
                 "@document_edit?module=$module&id=" . $document->get('id') . '&lang=' . $document->getCulture());
    ?>
    </p>
    <?php endif ?>
</div>
