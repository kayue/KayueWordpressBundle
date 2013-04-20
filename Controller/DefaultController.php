<?php

namespace Kayue\WordpressBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('KayueWordpressBundle:Default:index.html.twig', array('name' => $name));
    }
}
