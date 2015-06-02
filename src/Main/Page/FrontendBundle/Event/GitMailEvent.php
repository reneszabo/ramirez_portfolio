<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Main\Page\FrontendBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of GitMailEvent
 *
 * @author Rene Roberto Ramirez Szabo <rene.szabo@gmail.com>
 */
class GitMailEvent extends Event {

  const EVENT_NAME = 'git.mail.event';

  protected $request;

  public function __construct(Request $request) {
    $this->request = $request;
  }

  public function getRequest() {
    return $this->request;
  }

}
