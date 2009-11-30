<?php
/*
 * This file is part of the sfMapFishPlugin package.
 * (c) Camptocamp <info@camptocamp.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Generates a MapFish module.
 *
 * @package     sfMapFishPlugin
 * @author      Camptocamp <info@camptocamp.com>
 */
class sfMapFishGenerateModuleTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('application', sfCommandArgument::REQUIRED, 'The application name'),
      new sfCommandArgument('module', sfCommandArgument::REQUIRED, 'The module name'),
      new sfCommandArgument('model', sfCommandArgument::REQUIRED, 'The model class name'),
    ));

    $this->addOptions(array(
      new sfCommandOption('generate-route', null, sfCommandOption::PARAMETER_NONE, 'Whether you will update your routing.yml file')
    ));

    $this->aliases = array('mapfish-generate-crud', 'mapfish:generate-crud');
    $this->namespace = 'mapfish';
    $this->name = 'generate-module';
    $this->briefDescription = 'Generates a MapFish module';

    $this->detailedDescription = <<<EOF
The [mapfish:generate-module|INFO] task generates a Doctrine module:

  [./symfony mapfish:generate-module frontend article Article|INFO]

The task creates a [%module%|COMMENT] module in the [%application%|COMMENT] application
for the model class [%model%|COMMENT].

EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);
    
    $properties = parse_ini_file(sfConfig::get('sf_config_dir').'/properties.ini', true);

    $this->constants = array(
      'PROJECT_NAME'   => isset($properties['symfony']['name']) ? $properties['symfony']['name'] : 'symfony',
      'APP_NAME'       => $arguments['application'],
      'MODULE_NAME'    => $arguments['module'],
      'UC_MODULE_NAME' => ucfirst($arguments['module']),
      'MODEL_CLASS'    => $arguments['model'],
      'AUTHOR_NAME'    => isset($properties['symfony']['author']) ? $properties['symfony']['author'] : 'Your name here',
    );

    $this->executeGenerate($arguments, $options);
  }

  protected function executeGenerate($arguments = array(), $options = array())
  {
    // generate module
    $tmpDir = sfConfig::get('sf_cache_dir').'/'.'tmp'.'/'.md5(uniqid(rand(), true));
    $generatorManager = new sfGeneratorManager($this->configuration, $tmpDir);
    $generatorManager->generate('sfMapFishGenerator', array(
      'model_class'           => $arguments['model'],
      'moduleName'            => $arguments['module'],
    ));

    $moduleDir = sfConfig::get('sf_app_module_dir').'/'.$arguments['module'];

    // copy our generated module
    $this->getFilesystem()->mirror($tmpDir.'/'.'auto'.ucfirst($arguments['module']), $moduleDir, sfFinder::type('any'));

    // change module name
    $finder = sfFinder::type('file')->name('*.php');
    $this->getFilesystem()->replaceTokens($finder->in($moduleDir), '', '', array('auto'.ucfirst($arguments['module']) => $arguments['module']));

    // customize php and yml files
    $finder = sfFinder::type('file')->name('*.php', '*.yml');
    $this->getFilesystem()->replaceTokens($finder->in($moduleDir), '##', '##', $this->constants);

    // create basic test
    $this->getFilesystem()->copy(sfConfig::get('sf_symfony_lib_dir').'/task/generator/skeleton/module/test/actionsTest.php', sfConfig::get('sf_test_dir').'/functional/'.$arguments['application'].'/'.$arguments['module'].'ActionsTest.php');

    // customize test file
    $this->getFilesystem()->replaceTokens(sfConfig::get('sf_test_dir').'/functional/'.$arguments['application'].'/'.$arguments['module'].'ActionsTest.php', '##', '##', $this->constants);

    // update routing file
    if ($options['generate-route'])
    {
      $model = $arguments['model'];
      $name = strtolower(preg_replace(array('/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'), '\\1_\\2', $model));
      $name = $arguments['module'];

      $routing = sfConfig::get('sf_app_config_dir').'/routing.yml';
      $content = file_get_contents($routing);
      $routesArray = sfYaml::load($content);

      if (!isset($routesArray[$name]))
      {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $primaryKey = Doctrine::getTable($model)->getIdentifier();
        $module = $options['module'] ? $options['module'] : $name;
        $content = sprintf(<<<EOF
%s:
  url:                    /%s
  class: sfMapFishRouteCollection
  options:
    model:                %s
    module:               %s


EOF
        , $name, $module, $model, $module).$content;

        file_put_contents($routing, $content);
      }
    }

    // delete temp files
    $this->getFilesystem()->remove(sfFinder::type('any')->in($tmpDir));
  }

}
