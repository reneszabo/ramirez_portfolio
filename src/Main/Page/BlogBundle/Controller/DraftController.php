<?php

namespace Main\Page\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DraftController extends Controller {

  public function indexAction($draftNumber) {
    return $this->render('MainPageBlogBundle:Draft:draft-' . $draftNumber . '.html.twig');
  }

}
