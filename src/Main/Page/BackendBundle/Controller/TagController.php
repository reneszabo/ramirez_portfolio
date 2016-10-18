<?php

namespace Main\Page\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Main\EntityBundle\Entity\Tag;
use Main\Page\BackendBundle\Form\Type\TagType;

class TagController extends Controller {

  public function listAction(Request $request) {
    $tagRepository = $this->getDoctrine()->getRepository('MainEntityBundle:Tag');
    $tags = $tagRepository->findAll();
    return $this->render('MainPageBackendBundle:Tag:list.html.twig', array('tags' => $tags));
  }

  /**
   * @ParamConverter("tag", options={"mapping": {"slug": "slug"}})
   */
  public function formAction(Request $request, Tag $tag = null) {
    if (is_null($tag)) {
      $tag = new Tag();
      $url = $this->generateUrl('backend_tag_form_create', array());
    } else {
      $url = $this->generateUrl('backend_tag_form_edit', array('slug' => $tag->getSlug()));
    }
    $form = $this->createForm(
            new TagType(), $tag, array(
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
        $tagObj = $form->getData();
        $em = $this->getDoctrine()->getManager();
        $em->persist($tagObj);
        $em->flush();
        $this->get('session')->getFlashBag()->add('success', "Save Success");
        return $this->redirect($this->generateUrl('backend_tag_form_edit', array('slug' => $tagObj->getSlug())));
      } else {
        $this->get('session')->getFlashBag()->add('error', "Something is bad :(");
      }
    }

    return $this->render('MainPageBackendBundle:Tag:form.html.twig', array('tag' => $tag, 'form' => $form->createView()));
  }

}
