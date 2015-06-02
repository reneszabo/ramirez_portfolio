<?php

namespace InstagramBundle\TokenHandler;

/**
 * Interface UserTokenInterface
 *
 * Implement this on your User Entity to save the auth tokens
 *
 * @package InstagramBundle\TokenHandler
 */
interface UserTokenInterface {

  public function setInstagramAuthCode();

  public function getInstagramAuthCode();
}
