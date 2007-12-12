<?php

$dirname = dirname(__FILE__);

include($dirname.'/../bootstrap/unit.php');
include($dirname.'/../../config/config.php');
require_once($dirname.'/../../apps/frontend/lib/Language.class.php');
require_once($sf_symfony_lib_dir.'/util/sfYaml.class.php');

// load some fixtures
$app_yml = sfYaml::load($dirname.'/../../apps/frontend/config/app.yml');

$c2c_avaible_languages = $app_yml['all']['languages']['c2c'];
$forum_translations = $app_yml['all']['languages']['punbb'];

sfConfig::set('app_languages_c2c', $c2c_avaible_languages);
sfConfig::set('app_languages_punbb', $forum_translations);

// initiate lime_test
$t = new lime_test(8, new lime_output_color());

// ::getAll()
$t->diag('::getAll()');
$t->isa_ok(Language::getAll(), 'array', '::getAll() return an array');

// ::getPunBBLanguages()
$t->diag('::getPunBBLanguages()');
$t->isa_ok(Language::getPunBBLanguages(), 'array', '::getPunBBLanguages() return an array');

// ::translateForPunBB($symfony_lang)
$t->diag('::translateForPunBB($symfony_lang)');

foreach ($c2c_avaible_languages as $short_language_tag => $long_language_tag)
{
	$t->is(Language::translateForPunBB($short_language_tag),
	       $forum_translations[$short_language_tag],
	       'Language::translateForPunBB(' . $short_language_tag . ') return ' .
	       $forum_translations[$short_language_tag]);
}