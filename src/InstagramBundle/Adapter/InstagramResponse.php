<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace InstagramBundle\Adapter;

/**
 * Description of InstagramResponse
 *
 * @author Rene Ramirez <rene.ramirez@4pixles.co>
 */
class InstagramResponse {

  /**
   * The HTTP header in the response that holds the rate limit for this request
   */
  const RATE_LIMIT_HEADER = 'X-Ratelimit-Limit';

  /**
   * The HTTP header in the response that holds the rate limit remaingin
   */
  const RATE_LIMIT_REMAINING_HEADER = 'X-Ratelimit-Remaining';

  /** @var string The request url */
  public $url = '';

  /** @var array The request parameters */
  public $params = [];
 
  /** @var array The data collection  */
  public $data = [];

  /** @var array The meta collection */
  public $meta = [];

  /** @var array The pagination collection */
  public $pagination = [];

  /** @var array The user collection from OAuth requests */
  public $user = [];

  /** @var array The HTTP headers returned from API. */
  public $headers = [];

  /** @var string The raw JSON response from the API */
  public $json = NULL;

  /** @var int The number of requests you're allowed to make to the API */
  public $limit = 0;

  /** @var int The number of requests you have remaining for this client/access_token */
  public $remaining = 0;

  /**
   * @param \GuzzleHttp\Psr7\Response $response
   */
  public function __construct(\GuzzleHttp\Psr7\Response $response) {
    /* @var $headers \Guzzle\Http\Message\Header\HeaderCollection */
    /* @var $headerValue \Guzzle\Http\Message\Header */
    $this->headers = $response->getHeaders();
//    var_dump($this->headers);
    $this->json = json_decode($response->getBody()->getContents(),true);
//    var_dump($this->json);
    $this->data = isset($this->json['data']) ? $this->json['data'] : [];
    $this->meta = isset($this->json['meta']) ? $this->json['meta'] : [];
    if (isset($this->json['code']) && $this->json['code'] !== 200) {
      $this->meta = $this->json;
    }
    $this->pagination = isset($this->json['pagination']) ? $this->json['pagination'] : [];
    $this->user = isset($this->json['user']) ? $this->json['user'] : [];
    $this->limit = (isset($this->headers[self::RATE_LIMIT_HEADER])) ? $this->headers[self::RATE_LIMIT_HEADER][0] : 0;
    $this->remaining = (isset($this->headers[self::RATE_LIMIT_REMAINING_HEADER])) ? $this->headers[self::RATE_LIMIT_REMAINING_HEADER][0] : 0;
  }

}
