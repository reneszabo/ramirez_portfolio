<?php

namespace Main\Page\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Main\EntityBundle\Entity\Image;

class ImageController extends Controller {

  public function listAction(Request $request, $page = 1) {
    $imageRepo = $this->getDoctrine()->getRepository('MainEntityBundle:Image');
    $results = $imageRepo->findAllOrderByCreatedAt();
    $adapter = new \Pagerfanta\Adapter\ArrayAdapter($results);
    $pagerfanta = new \Pagerfanta\Pagerfanta($adapter);
    $pagerfanta->setMaxPerPage(15);    // We fix the number of results to 15 in each page.
    // if $page doesn't exist, we fix it to 1
    if (!$page) {
      $page = 1;
    }

    try {
      $pagerfanta->setCurrentPage($page);
    } catch (NotValidCurrentPageException $e) {
      throw new NotFoundHttpException();
    }
    return $this->render('MainPageBackendBundle:Image:list.html.twig', array('pager' => $pagerfanta));
  }

  public function createAction(Request $request) {
    $image = new Image();
    $uploadedFile = $request->files->get('file');
    $em = $this->getDoctrine()->getManager();
    /* @var $uploadableManager \Stof\DoctrineExtensionsBundle\Uploadable\UploadableManager */
    $uploadableManager = $this->get('stof_doctrine_extensions.uploadable.manager');
    $uploadableManager->markEntityToUpload($image, $uploadedFile);
    $em->persist($image);
    $em->flush();
    return new Response($image->getPath());
  }

}
