<?php

namespace Main\EntityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('MainEntityBundle:Default:index.html.twig', array('name' => $name));
    }
}
