<?php

namespace Main\Page\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

  public function indexAction() {
    $postRepository = $this->getDoctrine()->getRepository('MainEntityBundle:Post');
    $posts = $postRepository->findAll();
    return $this->render('MainPageBackendBundle:Default:index.html.twig', array('posts' => $posts));
  }

}
