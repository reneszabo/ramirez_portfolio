<?php

namespace Main\Page\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Main\EntityBundle\Entity\Post;
use Main\Page\BackendBundle\Form\Type\PostType;

class PostController extends Controller {

  /**
   * @ParamConverter("post", options={"mapping": {"slug": "slug"}})
   */
  public function formAction(Request $request, Post $post = null) {
    /* @var $user User */
    $user = $this->getUser();
    if (is_null($post)) {
      $post = new Post();
      $post->setAuthor($user);
      $url = $this->generateUrl('backend_post_form_create', array());
    } else {
      $url = $this->generateUrl('backend_post_form_edit', array('slug' => $post->getSlug()));
    }
    $form = $this->createForm(
            new PostType(), $post, array(
        'action' => $url,
        'method' => 'POST',
        'attr' => array(
            'rol' => 'form',
        ),
            )
    );


    $form->handleRequest($request);
    if ($form->isSubmitted()) {
      if ($form->isValid()) {
        $postObj = $form->getData();
        $em = $this->getDoctrine()->getManager();
        $em->persist($postObj);
        $em->flush();
        $this->get('session')->getFlashBag()->add('success', "Save Success");
        return $this->redirect($this->generateUrl('backend_homepage'));
      } else {
        $this->get('session')->getFlashBag()->add('error', "Something is bad :(");
      }
    }

    return $this->render('MainPageBackendBundle:Post:form.html.twig', array('post' => $post, 'form' => $form->createView()));
  }

}
