<?php

namespace Wbc\UtilityBundle;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class MailerManager.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @DI\Service("wbc.utility.mailer_manager")
 */
class MailerManager
{
    /**
     * @var string
     */
    private $env;

    /**
     * @var
     */
    private $templating;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var string
     */
    private $fromSenderEmailAddress;

    /**
     * @var string
     */
    private $fromSenderName;

    /**
     * MailerManager Constructor.
     *
     * @DI\InjectParams({
     * "kernel" = @DI\Inject("kernel"),
     * "templating" = @DI\Inject("templating"),
     * "mailer" = @DI\Inject("mailer"),
     * "fromSenderEmail" = @DI\Inject("%from_email%"),
     * "fromSenderName" = @DI\Inject("%from_sender_name%")
     * })
     *
     * @param KernelInterface $kernel
     * @param TwigEngine      $templating
     * @param \Swift_Mailer   $mailer
     * @param $fromSenderEmail
     * @param $fromSenderName
     */
    public function __construct(KernelInterface $kernel, TwigEngine $templating, \Swift_Mailer $mailer, $fromSenderEmail, $fromSenderName)
    {
        $this->env = $kernel->getEnvironment();
        $this->templating = $templating;
        $this->mailer = $mailer;
        $this->fromSenderEmailAddress = $fromSenderEmail;
        $this->fromSenderName = $fromSenderName;
    }

    /**
     * Sends an HTML email.
     *
     * @param string|array $to
     * @param string       $subject
     * @param string       $template
     * @param array        $parameters
     * @param mixed        $mimeType
     *
     * @throws \Exception
     * @throws \Twig_Error
     */
    public function sendByTemplate($to, $subject, $template, $parameters = [], $mimeType = 'text/html')
    {
        $this->send($to, $subject, $this->templating->render($template, $parameters), $mimeType);
    }

    /**
     * Sends a text email.
     *
     * @param string|array $to
     * @param string       $subject
     * @param string       $content
     * @param string       $mimeType
     */
    public function send($to, $subject, $content, $mimeType = 'text/plain')
    {
        if ($this->env !== 'prod') {
            return;
        }

        if (!is_array($to)) {
            $to = [$to];
        }

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom([$this->fromSenderEmailAddress], [$this->fromSenderName])
            ->setTo($to)
            ->setBody($content, $mimeType);

        $this->mailer->send($message);
    }
}
