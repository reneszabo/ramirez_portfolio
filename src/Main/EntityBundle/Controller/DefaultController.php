<?php

namespace Main\EntityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function loginAction()
    {
        return $this->render('MainEntityBundle:Default:login.html.twig');
    }
}
