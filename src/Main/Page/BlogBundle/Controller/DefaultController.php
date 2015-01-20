<?php

namespace Main\Page\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

  public function indexAction() {
    $postRepository = $this->getDoctrine()->getRepository('MainEntityBundle:Post');
    $posts = $postRepository->findAll();
    return $this->render('MainPageBlogBundle:Default:index.html.twig', array('posts' => $posts));
  }

}
