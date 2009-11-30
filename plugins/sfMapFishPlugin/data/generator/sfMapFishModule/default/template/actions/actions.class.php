[?php

/**
 * <?php echo $this->getModuleName() ?> actions.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage <?php echo $this->getModuleName()."\n" ?>
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class <?php echo $this->getGeneratedModuleName() ?>Actions extends sfMapFishActions
{
<?php include dirname(__FILE__).'/../../parts/indexAction.php' ?>

<?php include dirname(__FILE__).'/../../parts/showAction.php' ?>

<?php include dirname(__FILE__).'/../../parts/countAction.php' ?>

<?php include dirname(__FILE__).'/../../parts/createAction.php' ?>

<?php include dirname(__FILE__).'/../../parts/updateAction.php' ?>

<?php include dirname(__FILE__).'/../../parts/deleteAction.php' ?>

<?php include dirname(__FILE__).'/../../parts/processFormAction.php' ?>

}
