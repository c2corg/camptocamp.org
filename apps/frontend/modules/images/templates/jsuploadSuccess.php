<?php

if (!isset($plupload))
{
    include_partial('images/jsupload',
                    array('mod' => $sf_params->get('mod'),
                          'document_id' => $sf_params->get('document_id')));
}
else
{
    include_partial('images/plupload',
                    array('mod' => $sf_params->get('mod'),
                          'document_id' => $sf_params->get('document_id')));
}
