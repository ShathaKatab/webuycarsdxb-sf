<?php

declare(strict_types=1);

namespace Wbc\UtilityBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as CF;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wbc\BranchBundle\Entity\AppointmentReminder;

/**
 * Class TwilioReceiveSmsController.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class TwilioReceiveSmsController extends Controller
{
    /**
     * @CF\Route("/receive-sms", name="wbc_utility_twilio_receive_sms", methods={"POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function receiveSmsAction(Request $request)
    {
        //Do nothing for now
        return new Response();
        $messageBody = strtolower('');
        $mobileNumber = '';

        /** @var \Doctrine\ORM\EntityManager $entityManager */
        $entityManager = $this->get('doctrine.orm.entity_manager');
        $appointmentReminders = $entityManager->getRepository(AppointmentReminder::class)->findBy(['mobileNumber' => $mobileNumber], ['createdAt' => 'DESC'], 1);

        if (isset($appointmentReminders[0])) {
            $appointmentReminder = $appointmentReminders[0];

            if (in_array($messageBody, ['y', 'yes'], true)) {
                $appointmentReminder->setIsReschedule(true);
            }

            $appointmentReminder->setResponseText($messageBody);
            $entityManager->flush();
        }

        return new Response();
    }
}
