<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

pake_desc('optimizes symfony cache files for production environment');
pake_task('optimize', 'app_exists');

function run_optimize($task, $args)
{
  if (count($args) < 2)
  {
    throw new Exception('You must provide an application and an environment.');
  }

  $app = $args[0];
  $env = $args[1];

  $config = sprintf('cache/%s/%s/config/config_core_compile.yml.php', $app, $env);
  // simulate a request to populate config cache files
  // and get the current configuration
  define('SF_ROOT_DIR',    realpath('./'));
  define('SF_APP',         $app);
  define('SF_ENVIRONMENT', $env);
  define('SF_DEBUG',       false);

  require_once('apps'.DIRECTORY_SEPARATOR.$app.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

  if (!is_readable($config))
  {
    throw new Exception('Unable to optimize your config files');
  }
  
  pake_echo_action('optimize', "optimizing application $app in environment $env");
  $o = new sfOptimizer();
  $o->initialize(file_get_contents($config));
  $o->registerStandardOptimizers();
  file_put_contents($config, $o->optimize());
}
