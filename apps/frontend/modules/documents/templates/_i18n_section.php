<?php
use_helper('Language');
$module = $sf_context->getModuleName();
?>

<div class="article_contenu">
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
<br />
    <?php if ($document->isAvailable()): ?>
    <div class="article_infos_titre_contenu">
        <div class="article_contenu">
            <?php include_partial('i18n', array('document' => $document)) ?>
        </div>
    </div>
    
    <?php else: ?>
    <p>
    <?php
    echo __('This document is not available in %1%',
            array('%1%' => format_language_c2c($document->getCulture()))) .
         link_to(__('Create it!'),
                 "@document_edit?module=$module&id=" . $document->get('id') . '&lang=' . $document->getCulture());
    ?>
    </p>
    <div class="article_infos_titre_contenu">
        <div class="article_contenu"></div>
    </div>
    <?php endif ?>
</div>
</div>
