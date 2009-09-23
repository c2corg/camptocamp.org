<?php
use_helper('MyForm', 'Javascript');
echo global_form_errors_tag();
echo link_to_function(__('close'), '$(this).up().remove();');
// TODO i18n, bouton pour pouvoir le fermer / expliciter le nom du fichier qui a foiré
