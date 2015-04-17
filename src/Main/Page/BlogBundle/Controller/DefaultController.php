<?php

namespace Main\Page\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
Use Main\EntityBundle\Entity\Post;
Use Main\EntityBundle\Entity\User;
use Main\EntityBundle\Entity\Comment;
use Main\Page\BlogBundle\Form\Type\CommentType;

class DefaultController extends Controller {

  public function indexAction() {
    $postRepository = $this->getDoctrine()->getRepository('MainEntityBundle:Post');
    $posts = $postRepository->findBy(array(), array('createdAt' => 'DESC'));
    return $this->render('MainPageBlogBundle:Default:index.html.twig', array('posts' => $posts));
  }

  /**
   * @ParamConverter("post", options={"mapping": {"slug": "slug"}})
   */
  public function postAction(Request $request, Post $post = null) {
//    foreach ($post->getFiles() as $value) {
//      var_dump($value->getId(), $value->getPath(), $value->getOrderLike());
//    }
//    die();
    $form = $this->createCommentForm($post);
    return $this->render('MainPageBlogBundle:Default:single_post.html.twig', array('post' => $post, 'form' => $form->createView()));
  }

  /**
   * @ParamConverter("post", options={"mapping": {"slug": "slug"}})
   */
  public function postSecureAction(Request $request, Post $post = null) {
    $form = $this->createCommentForm($post);
    return $this->render('MainPageBlogBundle:Default:single_post.html.twig', array('post' => $post, 'form' => $form->createView()));
  }

  /**
   * @ParamConverter("post", options={"mapping": {"slug": "slug"}})
   */
  public function commentAction(Request $request, Post $post = null) {
    $form = $this->createCommentForm($post);

    $form->handleRequest($request);
    if ($form->isSubmitted()) {
      if ($form->isValid()) {
        $commentObj = $form->getData();
        $em = $this->getDoctrine()->getManager();
        $em->persist($commentObj);
        $em->flush();
        $this->get('session')->getFlashBag()->add('success', "Comment Success");
        return $this->redirect($this->generateUrl('blog_homepage_post', array('slug' => $post->getSlug())));
      } else {
        $this->get('session')->getFlashBag()->add('error', "Something is bad :(");
      }
    }

    return $this->render('MainPageBlogBundle:Default:single_post.html.twig', array('post' => $post, 'form' => $form->createView()));
  }

  private function createCommentForm($post) {
    $user = $this->getUser();
    if (is_null($user)) {
      $user = new User();
    }
    $comment = new Comment();
    $comment->setUser($user);
    $comment->setPost($post);
    $url = $this->generateUrl('blog_homepage_post_commet', array('slug' => $post->getSlug()));
    $form = $this->createForm(
            new CommentType(), $comment, array(
        'action' => $url,
        'method' => 'POST',
        'attr' => array(
            'rol' => 'form',
        ),
            )
    );
    return $form;
  }

}
