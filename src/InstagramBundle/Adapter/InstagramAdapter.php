<?php

namespace InstagramBundle\Adapter;

use Symfony\Component\Routing\RouterInterface;
use GuzzleHttp\Client;
use InstagramBundle\Adapter\InstagramResponse;
use Symfony\Bridge\Monolog\Logger;

class InstagramAdapter {

  /**
   * @var array Configuration for Instaphp 
   */
  protected $config = [];
  protected $client;
  protected $token = "";
  protected $logger;

  /**
   * Different construct to integrate better with Symfony2
   */
  public function __construct($config = array(), RouterInterface $router = null, Logger $logger) {
    $this->logger = $logger;
    $ua = sprintf('Ramirez-Portfolio/1.0; cURL/%s; (+http://ramirez-portfolio.com)', curl_version()['version']);
    $defaults = [
        'client_id' => '',
        'client_secret' => '',
        'access_token' => '',
        'redirect_uri' => '',
        'client_ip' => '',
        'scope' => 'comments+relationships+likes',
        'log_enabled' => true,
        'log_level' => Logger::DEBUG,
        'api_protocol' => 'https',
        'api_host' => 'api.instagram.com',
        'api_version' => 'v1',
        'http_useragent' => $ua,
        'http_timeout' => 6,
        'http_connect_timeout' => 2,
        'verify' => true,
        'debug' => false,
        'event.before' => [],
        'event.after' => [],
        'event.error' => [],
        'oauth_token_path' => 'oauth/access_token',
        'redirect_route' => 'instagram_callback',
    ];
    // If a router is passed, generate the redirect url from the route
    if ($router) {
      $config['redirect_uri'] = $router->generate($config['redirect_route'], array(), true);
    }
    $this->config = $config + $defaults;
    // Can't do anything without a client_id...
    if (empty($this->config['client_id'])) {
      throw new InstaphpException('Invalid client id');
    }
    $client = new Client([
        'base_uri' => $this->buildBasePath()
    ]);
    $this->client = $client;
  }

  protected function buildBasePath() {
    return $this->config['api_protocol'] . "://" . $this->config["api_host"];
  }

  /**
   * Adds the api_version to the beginning of the path
   * @param string $path
   * @param bool $add_version
   * @return string Returns the corrected path
   */
  protected function buildPath($path, $add_version = true) {
    $path = sprintf('/%s/', $path);
    $path = preg_replace('/[\/]{2,}/', '/', $path);
    if ($add_version && !preg_match('/^\/v1/', $path))
      $path = '/v1' . $path;
    return $path;
  }

  /**
   * @param \GuzzleHttp\Psr7\Response $response
   * @return InstagramResponse;
   */
  protected function handleResponse($response) {

    return new InstagramResponse($response);
  }

  /**
   * GET/tags/tag-name              - Get information about a tag object.
   * 
   * @param $tagname	Tag name.
   * @param $accessToken	A valid access token.
   * @return type
   */
  public function getTagInfo($tagname = "4pixles", $accessToken) {
    /* @var $request \Guzzle\Http\Message\Request */
    /* @var $response \Guzzle\Http\Message\Response */
    $request = $this->client->get($this->buildPath('tags') . $tagname);
    $q = $request->getQuery();
    $q['access_token'] = $accessToken;
    $response = $this->handleRequest($request);
    $aux = $this->handleResponse($response);
    return $aux;
  }

  /**
   * GET/tags/tag-name/media/recent - Get a list of recently tagged media.
   * 
   * @param $tagname	Tag name.
   * @param $accessToken	A valid access token.
   * @param $count	Count of tagged media to return.
   * @param $minTagID	Return media before this min_tag_id.
   * @param $maxTagID	Return media after this max_tag_id.
   * 
   * @return type
   */
  public function getTagRecent($tagname = "4pixles", $accessToken, $count = 5, $minTagID = null, $maxTagID = null) {
    /* @var $request \Guzzle\Http\Message\Request */
    $q = [];
    $q['access_token'] = $accessToken;
    if (!is_null($count)) {
      $q['count'] = $count;
    }
    if (!is_null($minTagID)) {
      $q['min_tag_id'] = $minTagID;
    }
    if (!is_null($maxTagID)) {
      $q['max_tag_id'] = $maxTagID;
    }
    $response = $this->client->request("GET", $this->buildPath('tags') . $tagname . '/media/recent', [
        'query' => $q
    ]);
    $aux = $this->handleResponse($response);
    return $aux;
  }

  /**
   * GET/tags/tag-name/media/recent - Get a list of recently tagged media.
   * 
   * @param $tagname	A valid tag name without a leading #. (eg. snowy, nofilter)
   * @param $accessToken	A valid access token.
   * @return type
   */
  public function getTagSearch($tagname = "4pixels", $accessToken) {
    /* @var $request \Guzzle\Http\Message\Request */
    $request = $this->client->get($this->buildPath('tags') . 'search');
    $q = $request->getQuery();
    $q['access_token'] = $accessToken;
    $q['q'] = $tagname;
    $response = $this->handleRequest($request);
    $aux = $this->handleResponse($response);
    return $aux;
  }

  public function getSubscription() {
    $q = [];
    $q['client_secret'] = $this->config['client_secret'];
    $q['client_id'] = $this->config['client_id'];
    $response = $this->client->request("GET", $this->buildPath('subscriptions'), [
        'query' => $q
    ]);
    $aux = $this->handleResponse($response);
    return $aux;
  }

  public function deleteSubscription($params) {
    $q = [];
    $q['client_secret'] = $this->config['client_secret'];
    $q['client_id'] = $this->config['client_id'];
    foreach ($params as $param => $value) {
      $q[$param] = $value;
    }
    $response = $this->client->request("DELETE", $this->buildPath('subscriptions'), [
        'query' => $q
    ]);
    $aux = $this->handleResponse($response);
    return $aux;
  }

  public function postSubscription($type, $redirectTo, $verifyToken, $params) {
    /* @var $request \Guzzle\Http\Message\Request */
    $signature = $this->buildPath('subscriptions', false);
    $q = [];
    $q['client_secret'] = $this->config['client_secret'];
    $q['client_id'] = $this->config['client_id'];
    $q['callback_url'] = $this->formatPath($redirectTo);
    $q['verify_token'] = $verifyToken;
    $q['object'] = $type;
    $q['aspect'] = 'media';
    foreach ($params as $param => $value) {
      $q[$param] = $value;
    }
    ksort($q);
    foreach ($q as $k => $v) {
      $signature.= "|$k=$v";
    }
    $q['sig'] = hash_hmac('sha256', $signature, $this->config['client_secret'], false);
    ;
    try {
      $response = $this->client->request(
              "POST"
              , $this->buildBasePath() . $this->buildPath('subscriptions')
              , [
          'form_params' =>
          $q
              ]
      );
    } catch (\GuzzleHttp\Exception\RequestException $ex) {
//      var_dump($ex->getResponse()->getBody()->getContents());
//      var_dump($ex->getResponse());
      $response = $ex->getResponse();
    }
    $aux = $this->handleResponse($response);
//    var_dump($response->getBody()->getContents());
    return $aux;
  }

  /**
   * Works like sprintf, but urlencodes it's arguments
   * @param string $path Path (in sprintf format)
   * @param mixed... $args Arguments to be urlencoded and passed to sprintf
   * @return string
   */
  protected function formatPath($path) {
    $args = func_get_args();
    $path = array_shift($args);
    $args = array_map('urlencode', $args);
    return vsprintf($path, $args);
  }

}
