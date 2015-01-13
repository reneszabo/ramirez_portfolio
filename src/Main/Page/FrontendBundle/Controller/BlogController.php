<?php

//CLAVE DE LA LLAVE DE GOOGLE notasecret

namespace Main\Page\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends Controller {
  public function indexAction() {
    return $this->render('MainPageFrontendBundle:Blog:index.html.twig', array());
  }
}
