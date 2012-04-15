<?php

/**
 * Cache class that stores content in a memcached k/v store. Based on the
 * sqlite store.
 *
 * @package    symfony
 * @subpackage cache
 * @author     Marc Fournier <marc.fournier@camptocamp.org>
 * @version    SVN: $Id$
 */
class sfMemcacheCache extends sfCache
{

  protected $memcache = null;
  protected $compress = null;
  protected $debug = null;


 /**
  * Constructor.
  *
  * @param string The database name
  */
  public function __construct($database = null)
  {
    if (!extension_loaded('memcache'))
    {
      throw new sfConfigurationException('sfMemcacheCache class needs "memcache" extension');
    }

  }

 /**
   * Initializes the cache.
   *
   * @param array An array of options
   * Available options:
   *  - database:                database name
   *  - automaticCleaningFactor: disable / tune automatic cleaning process (int)
   *
   */
  public function initialize($options = array())
  {

    $this->memcache = new Memcache();
    $this->debug    = isset($options['debug']) ? $options['debug'] : null;
    $this->compress = isset($options['compress']) ? $options['compress'] : true;

    foreach ($options['servers'] as $server)
    {
      $port = isset($server['port']) ? $server['port'] : 11211;
        if (!$this->memcache->addServer($server['host'], $port, isset($server['persistent']) ? $server['persistent'] : true))
        {
          throw new sfInitializationException(sprintf('Unable to connect to the memcache server (%s:%s).', $server['host'], $port));
        }
    }
  }


 /**
   * Destructor.
   */
  #public function __destruct()
  #{
  #  sqlite_close($this->conn);
  #}

 /**
  * Tests if a cache is available and (if yes) returns it.
  *
  * @param  string  The cache id
  * @param  string  The name of the cache namespace
  * @param  boolean If set to true, the cache validity won't be tested
  *
  * @return string  The data in the cache (or null if no cache available)
  *
  * @see sfCache
  */
  public function get($id, $namespace = self::DEFAULT_NAMESPACE, $doNotTestCacheValidity = false)
  {
    $default = null;
    $value = $this->memcache->get($this->keyName($id, $namespace));

    if ($this->debug) { $this->debugMsg("GET", $id, $namespace, $value); }
    return false === $value ? $default : $value;
  }

  /**
   * Returns true if there is a cache for the given id and namespace.
   *
   * @param  string  The cache id
   * @param  string  The name of the cache namespace
   * @param  boolean If set to true, the cache validity won't be tested
   *
   * @return boolean true if the cache exists, false otherwise
   *
   * @see sfCache
   */
  public function has($id, $namespace = self::DEFAULT_NAMESPACE, $doNotTestCacheValidity = false)
  {
    if ($this->debug) { $this->debugMsg("HAS", $id, $namespace); }
    return !(false === $this->memcache->get($this->keyName($id, $namespace)));
  }

 /**
  * Saves some data in the cache.
  *
  * @param string The cache id
  * @param string The name of the cache namespace
  * @param string The data to put in cache
  *
  * @return boolean true if no problem
  *
  * @see sfCache
  */
  public function set($id, $namespace = self::DEFAULT_NAMESPACE, $data)
  {
    $lifetime = (isset($this->lifeTime) && is_int($this->lifeTime)) ? $this->lifeTime : 0;

    // avoid lifetime > 1 month, which is unsupported
    if ($lifetime >= 3600 * 24 * 30 ) { $lifetime = 0; }

    if ($this->debug) { $this->debugMsg("SET", $id, $namespace, $data); }
    return $this->memcache->set($this->keyName($id, $namespace), $data, $this->compress, $lifetime);
  }

 /**
  * Removes an element from the cache.
  *
  * @param string The cache id
  * @param string The name of the cache namespace
  *
  * @return boolean true if no problem
  *
  * @see sfCache
  */
  public function remove($id, $namespace = self::DEFAULT_NAMESPACE)
  {
    if ($this->debug) { $this->debugMsg("REMOVE", $id, $namespace); }
    return $this->memcache->delete($this->keyName($id, $namespace));
  }

 /**
  * Cleans the cache.
  *
  * If no namespace is specified all cache files will be destroyed
  * else only cache files of the specified namespace will be destroyed.
  *
  * @param string The name of the cache namespace
  *
  * @return boolean true if no problem
  */
  public function clean($namespace = null, $mode = 'all')
  {
    if ('all' === $mode)
    {
      return $this->memcache->flush();
    }
  }

  public function lastModified($id, $namespace = self::DEFAULT_NAMESPACE)
  {
    if ($this->debug) { $this->debugMsg("LASTMOD", $id, $namespace); }
    return 0;
  }

 /**
  * @return an md5 string
  */
  protected function keyName($id, $namespace)
  {
    return md5("sf_" . $id . $namespace);
  }

  protected function debugMsg($cmd, $id, $namespace, $value=null)
  {
    error_log("$cmd: id  = '$id'");
    error_log("$cmd: ns  = '$namespace'");
    error_log("$cmd: key = " . $this->keyName($id, $namespace));
    #error_log("$cmd: val = " . ($value ? $value : "no value"));
    error_log("limetime = " . $this->lifeTime . " refresh = " . date('c', $this->refreshTime));
    #if (sfConfig::get('sf_logging_enabled'))
    #{
    #  sfContext::getInstance()->getLogger()->info($message);
    #}
  }

}
