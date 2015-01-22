<?php

namespace Main\Page\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Main\EntityBundle\Entity\Image;

class ImageController extends Controller {

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
