<?php

/*
 * This file is part of the sfWebBrowserPlugin package.
 * (c) 2004-2006 Francois Zaninotto <francois.zaninotto@symfony-project.com>
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com> for the click-related functions
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWebBrowser provides a basic HTTP client.
 *
 * @package    sfWebBrowserPlugin
 * @author     Francois Zaninotto <francois.zaninotto@symfony-project.com>
 * @author     Ben Meynell <bmeynell@colorado.edu>
 * @version    0.9
 */

class sfCurlAdapter
{

  protected
    $options = array(),
    $headers = array();

  public function __construct($options = array())
  {
    if (!function_exists('curl_init'))
    {
      throw new Exception('Curl not enabled');
    }

    $this->options = $options;
  }

  /**
   * Submits a request
   *
   * @param string  The request uri
   * @param string  The request method
   * @param array   The request parameters (associative array)
   * @param array   The request headers (associative array)
   *
   * @return sfWebBrowser The current browser object
   */  
  public function call($browser, $uri, $method = 'GET', $parameters = array(), $headers = array())
  {
    $curl = curl_init();
    
    // default settings 
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_AUTOREFERER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

    // uri
    curl_setopt($curl, CURLOPT_URL, $uri);

    // request headers
    $m_headers = array_merge($browser->getDefaultRequestHeaders(), $browser->initializeRequestHeaders($headers));
    $request_headers = explode("\r\n", $browser->prepareHeaders($m_headers));
    curl_setopt($curl, CURLOPT_HTTPHEADER, $request_headers);

    // encoding support
    isset($headers['Accept-Encoding']) ? curl_setopt($curl, CURLOPT_ENCODING, $headers['Accept-Encoding']) : null;

    // store response headers, uses callback function
    curl_setopt($curl, CURLOPT_HEADERFUNCTION, array($this, 'read_header'));

    // handle any request method
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($parameters, '', '&')); 
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method); 

    $response = curl_exec($curl);

    if (curl_errno($curl))
    {
      throw new Exception(curl_error($curl));
    }

    $requestInfo = curl_getinfo($curl);

    $browser->setResponseCode($requestInfo['http_code']);
    $browser->setResponseHeaders($this->headers);
    $browser->setResponseText($response);

    // clear response headers
    $this->headers = array();

    curl_close($curl);

    return $browser;
  }

  private function read_header($curl, $headers)
  {
    $this->headers[] = $headers;
    return strlen($headers);
  }

}
