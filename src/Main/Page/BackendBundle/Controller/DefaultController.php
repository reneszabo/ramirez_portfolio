<?php

namespace Main\Page\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Main\EntityBundle\Entity\Comment;

class DefaultController extends Controller {

  public function indexAction() {
    $postRepository = $this->getDoctrine()->getRepository('MainEntityBundle:Post');
    $commentRepository = $this->getDoctrine()->getRepository('MainEntityBundle:Comment');
    $posts = $postRepository->findAll();
    $comments = $commentRepository->findBy(array(), array('createdAt' => 'ASC'), 10);
    return $this->render('MainPageBackendBundle:Default:index.html.twig', array('posts' => $posts, 'comments' => $comments));
  }

}
