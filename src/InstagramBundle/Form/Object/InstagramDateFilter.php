<?php

namespace InstagramBundle\Form\Object;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of InstagramDateFormFilter
 *
 * @author Rene Roberto Ramirez Szabo <rene.szabo@gmail.com>
 */
class InstagramDateFilter {

  private $createdTimeStart;
  private $createdTimeEnd;

  function getCreatedTimeStart() {
    return $this->createdTimeStart;
  }

  function getCreatedTimeEnd() {
    return $this->createdTimeEnd;
  }

  function setCreatedTimeStart($createdTimeStart) {
    $this->createdTimeStart = $createdTimeStart;
  }

  function setCreatedTimeEnd($createdTimeEnd) {
    $this->createdTimeEnd = $createdTimeEnd;
  }


}
