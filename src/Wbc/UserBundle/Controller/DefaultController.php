<?php

namespace Wbc\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('WbcUserBundle:Default:index.html.twig');
    }
}
