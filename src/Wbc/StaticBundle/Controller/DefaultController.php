<?php

namespace Wbc\StaticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as CF;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class DefaultController.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @CF\Template()
 */
class DefaultController extends Controller
{
    /**
     * Homepage.
     *
     * @CF\Route("", name="wbc_static_default_index")
     * @CF\Method("GET")
     *
     * @return array
     */
    public function indexAction()
    {
        return [];
    }

    /**
     * @CF\Route("/contact-us", name="wbc_static_default_contact_us")
     *
     * @return array
     */
    public function contactUsAction()
    {
        return [];
    }

    /**
     * @CF\Route("/{slug}", requirements={"slug": "[a-zA-Z1-9\-_\/]+"}, name="wbc_static_default_article")
     */
    public function articleAction($slug)
    {
        $slug = strtolower($slug);
        $template = sprintf('WbcStaticBundle:markdown:%s.md.twig', $slug);
        $templating = $this->container->get('templating');

        if (!$templating->exists($template)) {
            throw new NotFoundHttpException('Page not found!');
        }

        return ['content' => $templating->render($template)];
    }
}
