<?php

namespace Wbc\BranchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('WbcBranchBundle:Default:index.html.twig');
    }
}
