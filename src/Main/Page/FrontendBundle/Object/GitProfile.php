<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Main\Page\FrontendBundle\Object;

/**
 * Description of GitProfile
 *
 * @author Rene Roberto Ramirez Szabo <rene.szabo@gmail.com>
 */
class GitProfile {

  protected $login;
  protected $email = '';
  protected $avatar_url;
  protected $name;
  protected $html_url;
  protected $pusher;
  protected $img;
  function getImg() {
    return $this->img;
  }

  function setImg($img) {
    $this->img = $img;
  }

    public function __toString() {
    return $this->login . ' - ' . $this->email . ' - ' . $this->avatar_url . ' - ' . $this->name . ' - ' . $this->html_url;
  }

  function __construct($login, $email, $avatar_url, $name, $html_url, $pusher) {
    $this->login = $login;
    $this->email = $email;
    $this->avatar_url = $avatar_url;
    $this->name = $name;
    $this->html_url = $html_url;
    $this->pusher = $pusher;
  }
  function getPusher() {
    return $this->pusher;
  }

  function setPusher($pusher) {
    $this->pusher = $pusher;
  }

    function getLogin() {
    return $this->login;
  }

  function getEmail() {
    return $this->email;
  }

  function getAvatar_url() {
    return $this->avatar_url;
  }

  function getName() {
    return $this->name;
  }

  function getHtml_url() {
    return $this->html_url;
  }

  function setLogin($login) {
    $this->login = $login;
  }

  function setEmail($email) {
    $this->email = $email;
  }

  function setAvatar_url($avatar_url) {
    $this->avatar_url = $avatar_url;
  }

  function setName($name) {
    $this->name = $name;
  }

  function setHtml_url($html_url) {
    $this->html_url = $html_url;
  }

  function hasEmail() {
    if ($this->getEmail() != '') {
      return true;
    } else {
      return false;
    }
  }

}
