<?php

namespace Wbc\StaticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as CF;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Wbc\StaticBundle\Form\ContactUsType;

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
     * Contact Us.
     *
     * @CF\Route("/contact-us", name="wbc_static_default_contact_us")
     * @CF\Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return array
     */
    public function contactUsAction(Request $request)
    {
        $form = null;

        if ($request->getMethod() == Request::METHOD_POST) {
            $data = $request->request->all();

            $form = $this->createForm(new ContactUsType());

            $form->submit($data);

            if ($form->isValid()) {
                $formData = $form->getData();

                $message = \Swift_Message::newInstance()
                    ->setSubject(sprintf('New Message from Contact Us on %s', $this->getParameter('site_title')))
                    ->setFrom([$this->getParameter('from_email')], [$this->getParameter('from_sender_name')])
                    ->setTo($this->getParameter('contact_us_email'))
                    ->setBody($this->renderView('Emails/contactUs.html.twig', [
                        'name' => $formData['name'],
                        'emailAddress' => $formData['emailAddress'],
                        'phoneNumber' => $formData['phoneNumber'],
                        'message' => $formData['message'],
                    ]),
                        'text/html');

                $this->get('mailer')->send($message);

                $this->addFlash('success', 'Your contact message has been sent!');

                return $this->redirect($this->generateUrl('wbc_static_default_index'));
            }
        }

        return ['form' => $form ? $form->createView() : null];
    }

    /**
     * Article.
     *
     * @CF\Route("/{slug}", requirements={"slug": "[a-zA-Z1-9\-_\/]+"}, name="wbc_static_default_article")
     * @CF\Method("GET")
     */
    public function articleAction($slug)
    {
        $slug = strtolower($slug);
        $template = sprintf('WbcStaticBundle:markdown:%s.md.twig', $slug);
        $templating = $this->container->get('templating');
        $metaPath = sprintf('WbcStaticBundle:markdown/meta:%s.html.twig', $slug);

        if (!$templating->exists($template)) {
            throw new NotFoundHttpException('Page not found!');
        }

        $data = ['content' => $templating->render($template)];

        if ($templating->exists($metaPath)) {
            $data['metaPath'] = $metaPath;
        }

        return $data;
    }
}
