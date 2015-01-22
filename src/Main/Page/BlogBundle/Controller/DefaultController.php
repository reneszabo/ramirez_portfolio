<?php

namespace Main\Page\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
Use Main\EntityBundle\Entity\Post;

class DefaultController extends Controller {

  public function indexAction() {
    $postRepository = $this->getDoctrine()->getRepository('MainEntityBundle:Post');
    $posts = $postRepository->findAll();
    return $this->render('MainPageBlogBundle:Default:index.html.twig', array('posts' => $posts));
  }

  /**
   * @ParamConverter("post", options={"mapping": {"slug": "slug"}})
   */
  public function postAction(Request $request, Post $post = null) {
    return $this->render('MainPageBlogBundle:Default:single_post.html.twig', array('post' => $post));
  }

}
