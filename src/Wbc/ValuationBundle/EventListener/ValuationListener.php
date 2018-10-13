<?php

declare(strict_types=1);

namespace Wbc\ValuationBundle\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Kernel;
use Wbc\StaticBundle\EventListener\StaticListener;
use Wbc\StaticBundle\ParameterManager;
use Wbc\UtilityBundle\MailerManager;
use Wbc\ValuationBundle\Entity\Valuation;
use Wbc\ValuationBundle\ValuationEvent;
use Wbc\ValuationBundle\ValuationEvents;
use Wbc\ValuationBundle\ValuationManager;

/**
 * Class ValuationListener.
 *
 * @DI\DoctrineListener(
 *     events = {"prePersist", "postPersist", "postUpdate"},
 *     connection = "default",
 *     lazy = true,
 *     priority = 0,
 * )
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class ValuationListener
{
    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var ValuationManager
     */
    private $valuationManager;

    /**
     * @var MailerManager
     */
    private $mailerManager;

    /**
     * @var array
     */
    private $valuationEmails;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var ParameterManager
     */
    private $parameterManager;

    /**
     * ValuationListener Constructor.
     *
     * @DI\InjectParams({
     * "kernel" = @DI\Inject("kernel"),
     * "valuationManager" = @DI\Inject("wbc.valuation_manager"),
     * "mailerManager" = @DI\Inject("wbc.utility.mailer_manager"),
     * "session" = @DI\Inject("session"),
     * "parameterManager" = @DI\Inject("wbc.static.parameter_manager"),
     * "valuationEmails" = @DI\Inject("%valuation_emails%")
     * })
     *
     * @param Kernel           $kernel
     * @param ValuationManager $valuationManager
     * @param MailerManager    $mailerManager
     * @param Session          $session
     * @param ParameterManager $parameterManager
     * @param array            $valuationEmails
     */
    public function __construct(Kernel $kernel,
                                ValuationManager $valuationManager,
                                MailerManager $mailerManager,
                                Session $session,
                                ParameterManager $parameterManager,
                                array $valuationEmails)
    {
        $this->kernel = $kernel;
        $this->valuationManager = $valuationManager;
        $this->mailerManager = $mailerManager;
        $this->valuationEmails = $valuationEmails;
        $this->session = $session;
        $this->parameterManager = $parameterManager;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$object instanceof Valuation || !$this->session->has(StaticListener::UTM_SOURCE)) {
            return;
        }

        $utmSource = $this->session->get(StaticListener::UTM_SOURCE);
        $this->parameterManager->addValuationSource($utmSource);
        $object->setSource($this->parameterManager->websitifySource($utmSource));
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$object instanceof Valuation) {
            return;
        }

        $this->setValuationPrice($object);
        $this->mailerManager->sendByTemplate($this->valuationEmails, 'New Valuation', 'Emails/adminNewValuation.html.twig', ['valuation' => $object]);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$object instanceof Valuation) {
            return;
        }

        $this->setValuationPrice($object);
    }

    /**
     * @DI\Observe(ValuationEvents::VALUATION_REQUESTED_FRONT_END)
     *
     * @param ValuationEvent $event
     */
    public function onValuationRequestedFrontEnd(ValuationEvent $event): void
    {
        $valuation = $event->getValuation();
        $priceOnline = $valuation->getPriceOnline();

        if (!$priceOnline) {
            return;
        }

        $font = $this->kernel->getRootDir().'/../web/fonts/somatic-rounded/Somatic-Rounded.ttf';
        $fontSize = 40;
        $width = 360;
        $height = 50;

        $im = imagecreate($width, $height);
        $white = imagecolorallocate($im, 255, 255, 255);
        $black = imagecolorallocate($im, 0, 0, 0);
        imagefilledrectangle($im, 0, 0, 399, 29, $white);
        $text = sprintf('AED %s', number_format($priceOnline));
        $textBox = imagettfbbox($fontSize, 0, $font, $text);
        $textWidth = abs(max($textBox[2], $textBox[4]));
        $textHeight = abs(max($textBox[5], $textBox[7]));
        $x = (int) round((imagesx($im) - $textWidth) / 2);
        $y = (int) round(((imagesy($im) + $textHeight) / 2.1));

        imagettftext($im, $fontSize, 0, $x, $y, $black, $font, $text);
        imagecolortransparent($im, $white);

        ob_start();
        imagepng($im);
        $imageString = ob_get_clean();
        imagedestroy($im);

        $valuation->setPriceImageEncoded(sprintf('data:image/png;base64,%s', base64_encode($imageString)));
    }

    /**
     * @param Valuation $valuation
     */
    private function setValuationPrice(Valuation $valuation): void
    {
        $this->valuationManager->setPrice($valuation);
    }
}
