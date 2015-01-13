<?php

namespace Main\Page\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MainPageBlogBundle:Default:index.html.twig', array());
    }
}
