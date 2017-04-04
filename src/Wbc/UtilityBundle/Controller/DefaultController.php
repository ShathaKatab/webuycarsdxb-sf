<?php

namespace Wbc\UtilityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('WbcUtilityBundle:Default:index.html.twig');
    }
}
