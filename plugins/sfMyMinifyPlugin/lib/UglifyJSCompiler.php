<?php

/**
 * Simple class for compressing Javascript using UglifyJS
 * @author Lionel
 */
class Minify_UglifyJSCompiler {

  public static $nodeExecutable = 'nodejs';
  public static $UglifyJSExecutable = null;
  public static $tempDir = null;

  public static function minify($js, $options = array()) {
    self::_prepare();

    if (!($tmpFile = tempnam(self::$tempDir, 'ujsc_'))) {
      throw new Exception('Minify UglifyJS Compiler: could not create temp file');
    }

    file_put_contents($tmpFile, $js);
    exec(self::_getCmd($options, $tmpFile), $output, $result_code);
    unlink($tmpFile);

    if ($result_code != 0) {
      throw new Exception('Minify UglifyJS Compiler: execution failed');
    }
    return implode("\n", $output);
  }

  private static function _getCmd($userOptions, $file) {
    return self::$nodeExecutable . ' ' . escapeshellarg(self::$UglifyJSExecutable) .
      ' -nc ' . escapeshellarg($file);
  }

  private static function _prepare() {
    if (!is_file(self::$UglifyJSExecutable)) {
      throw new Exception('Minify UglifyJS Compiler: could not find uglifyjs (' . self::$UglifyJSExecutable . ')');
    }
  }

}
