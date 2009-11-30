<?php

require_once dirname(__FILE__).'/../lib/BaseprintActions.class.php';

/**
 * print actions.
 * 
 * @package    sfMapFishPlugin
 * @subpackage print
 * @author     Camptocamp <info@camptocamp.com>
 */
class mfPrintActions extends BaseprintActions
{
  
  /**
   * Temporary file prefix / suffix
   *
   * @var string
   */
  protected $_temp_file_prefix = 'mfPrintTempFile_';
  protected $_temp_file_suffix = '';


  protected $_java_bin;

  /**
   * Delay in seconds before a temporary file is deleted
   *
   * @var integer
   */
  protected $_temp_file_purge_seconds = 300;

  /**
   * MF config
   */
  protected $jarPath; 
  protected $configPath;
  protected $tmpDir;

  /**
   * Set MF config variables
   *
   * @see sfAction#preExecute()
   */
  public function preExecute()
  {
    $this->jarPath = sfConfig::get('mf_print_jar');
    $this->configPath = sfConfig::get('mf_print_cfg');
    $this->tmpDir = sfConfig::get('sf_cache_dir');
    $this->_java_bin = sfConfig::get('mf_jre_path', '');
  }

  private function mycmd_exec($cmd, $input, &$stdout=array(), &$stderr=array())
  {
    $outfile = tempnam($this->tmpDir, "cmd"); 
    $errfile = tempnam($this->tmpDir, "cmd");

    $descriptorspec = array(
      0 => array("pipe", "r"),
      1 => array("file", $outfile, "w"),
      2 => array("file", $errfile, "w")
    );
    if (sfConfig::get('sf_logging_enabled'))
    {     
      sfContext::getInstance()->getLogger()->debug('{mf_print} Execute : '.$cmd);
    }

    $proc = proc_open($cmd, $descriptorspec, $pipes);

    if (!is_resource($proc)) return 255;
    if ($input) {
      fwrite($pipes[0], $input);
    }
    fclose($pipes[0]);
    $exit = proc_close($proc);

    $stdout = file($outfile);
    $stderr = file($errfile);
    if (sfConfig::get('sf_logging_enabled'))
    {     
      sfContext::getInstance()->getLogger()->debug('{mf_print} '.implode("",$stderr));
    }

    unlink($outfile);
    unlink($errfile);

    return $exit;
  }

  /**
   * Get print info for widget initialization
   * 
   * @param sfRequest $request
   *
   * @return sfView::NONE
   */
  public function executeInfo() 
  {
    $pattern = '%sjava -Djava.awt.headless=true -cp "%s" org.mapfish.print.ShellMapPrinter'.
      ' --config="%s" --clientConfig --verbose=0';
    $cmd = sprintf($pattern, $this->_java_bin, $this->jarPath, $this->configPath);

    $stdout = $stderr = array();
    $return = $this->mycmd_exec($cmd, null, $stdout, $stderr);
    
    $this->forward404unless($return == 0);
    
    $object = json_decode($stdout[0], true);
    $this->_addURLs($object);
    
    return $this->renderText(json_encode($object));
  }

  /**
  * All in one method: creates and returns the PDF to the client.
  *
  * @param sfRequest $request
  *
  * @return sfView::NONE
  */
  public function executeDoprint() 
  {
    $pattern = '%sjava -Djava.awt.headless=true -cp "%s" org.mapfish.print.ShellMapPrinter'.
      ' --config="%s" --clientConfig --verbose=0';
    $cmd = sprintf($pattern, $this->_java_bin, $this->jarPath, $this->configPath);
    $stdout = $stderr = array();
    $return = $this->mycmd_exec($cmd, $request->getRawBody(), $stdout, $stderr);

    $this->forward404unless($return == 0);

    $pdf = implode('', $stdout);
    return $this->renderPDF($pdf);
  }

  /**
   * Creates the PDF and returns to the client (in JSON) the URL to get it.
   */
  public function executeCreate(sfWebRequest $request)
  {
    sfLoader::loadHelpers('Url'); // FIXME: deprecated

    $this->_purgeOldFiles();
    $pdf_path = tempnam($this->tmpDir, $this->_temp_file_prefix);

    $pattern = '%sjava -Djava.awt.headless=true -cp "%s" org.mapfish.print.ShellMapPrinter'.
      ' --config="%s" --verbose=0 --output=%s';
    $cmd = sprintf($pattern, $this->_java_bin, $this->jarPath, $this->configPath, $pdf_path);

    $stdout = $stderr =array();
    
    $return = $this->mycmd_exec($cmd, $request->getRawBody(), $stdout, $stderr);

    $this->forward404unless($return == 0);
    
    $curId = str_replace($this->_temp_file_prefix, '', basename($pdf_path));
    $out = array('getURL' => url_for('mfPrint/get?id='.$curId));
    
    return $this->renderText(json_encode($out));
  }

  /**
   * To get the previously created PDF.
   */
  public function executeGet(sfRequest $request) 
  {
    $this->forward404unless($id = $request->getParameter('id'));

    $pdf_path = $this->tmpDir.'/'.$this->_temp_file_prefix.$id;

    $this->forward404unless($pdf = file_get_contents($pdf_path));

    return $this->renderPDF($pdf);
  }

  private function _addURLs(&$object) 
  {
    sfLoader::loadHelpers(array('Url')); // FIXME
    $object['printURL'] = url_for('mfPrint/doprint', true);
    $object['createURL'] = url_for('mfPrint/create', true);
  }

  /**
   * Delete temporary files that are more than $this->_temp_file_purge_seconds seconds old
   */
  private function _purgeOldFiles() 
  {
    $pdfs = glob($this->tmpDir.'/'.$this->_temp_file_prefix.'*');
    foreach ($pdfs as $pdf)
    {
      if (round(time() - filemtime($pdf)) > $this->_temp_file_purge_seconds) 
      {
        unlink($pdf);
      }
    }
  }

  /**
   * Returns PDF file whith appropriate headers
   *
   * @param string $name The file name
   * @param string $content The file content
   *
   * @return sfView::NONE
   */
  public function renderPDF($content)
  {
    $response = $this->getResponse();
    $response->clearHttpHeaders(); 
    $response->setContentType('application/pdf');            
    $response->setHttpHeader("Content-Disposition", 'attachment; filename="print.pdf"');
    $response->setHttpHeader("Pragma", 'public');
    $response->setHttpHeader("Expires", '0');
    $response->setHttpHeader("Cache-Control", 'private');
    $response->setHttpHeader("Content-Length", strlen($content));
    return $this->renderText($content);
  }

}
