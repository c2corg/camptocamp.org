<?php

if ($plupload)
{
    use_stylesheet('/static/css/plupload.css', 'custom');
    include_partial('images/plupload',
                    array('mod' => $sf_params->get('mod'),
                          'document_id' => $sf_params->get('document_id')));
}
else
{
    include_partial('images/jsupload',
                    array('mod' => $sf_params->get('mod'),
                          'document_id' => $sf_params->get('document_id')));
}
