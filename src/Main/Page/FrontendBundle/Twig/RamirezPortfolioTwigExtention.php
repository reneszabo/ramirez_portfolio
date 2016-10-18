<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Main\Page\FrontendBundle\Twig;

/**
 * Description of RamirezPortfolioTwigExtention
 *
 * @author Rene Roberto Ramirez Szabo <rene.szabo@gmail.com>
 */
class RamirezPortfolioTwigExtention extends \Twig_Extension {

  public function getFilters() {
    return array(
        new \Twig_SimpleFilter('timeKnow', array($this, 'getTimeKnow')),
        new \Twig_SimpleFilter('age', array($this, 'getAge')),
    );
  }

  public function getAge($date) {
    if (!$date instanceof \DateTime) {
      // turn $date into a valid \DateTime object
      $date = date($date);
      $date = new \DateTime($date);
    }

    $referenceDate = date('01-01-Y');
    $referenceDateTimeObject = new \DateTime($referenceDate);

    $diff = $referenceDateTimeObject->diff($date);

    return $diff->y;
  }

  public function getTimeKnow($date, $date2 = null) {
    if (!$date instanceof \DateTime) {
      // turn $date into a valid \DateTime object
      $datem = date($date);
      $datem2 = new \DateTime($datem);
    }
    if (is_null($date2)) {
      $referenceDate = date('Y-m-d');
      $referenceDateTimeObject = new \DateTime($referenceDate);
    } else {
      if (!$date2 instanceof \DateTime) {
        $referenceDate = date($date2);
        $referenceDateTimeObject = new \DateTime($referenceDate);
      } else {
        $referenceDateTimeObject = $date2;
      }
    }

    $diff = $datem2->diff($referenceDateTimeObject);
    $days = $diff->format('%a');
    if ($days > 360) {
      $years = $diff->format('%y');
      if ($years <= 1) {
        return "1 year";
      }
      return $diff->format('%R%y years');
    }
    return $diff->format('%R%a days');
  }

  public function getName() {
    return 'ramirez_portfolio_extension';
  }

}
