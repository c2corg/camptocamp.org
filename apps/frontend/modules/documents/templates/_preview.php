<?php use_helper('Javascript') ?>

<div id="preview" style="display:none;">
</div>

<?php if ($concurrent_edition){
echo javascript_tag(
        remote_function(array('update' => 'preview',
                            'url' => $sf_context->getModuleName() . "/ViewCurrent?id=$id&lang=$lang",
                            'method' => 'post',
                            'loading' => "Element.show('indicator')",
                            'complete' => "Element.show('preview'); new Effect.ScrollTo('preview', {offset: -35});" . 
                                        visual_effect('highlight', 'preview') . 
                                        "Element.hide('indicator');")));} ?>
