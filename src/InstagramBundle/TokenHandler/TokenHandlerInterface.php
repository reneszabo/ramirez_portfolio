<?php

namespace InstagramBundle\TokenHandler;

interface TokenHandlerInterface {

  /**
   * return the token however you want
   * @return string/object The token to pass to Instaphp
   */
  public function getToken();

  /**
   * check to see if this user logged in
   * @return boolean
   */
  public function isLoggedIn();
}
