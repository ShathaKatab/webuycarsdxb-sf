<?php

namespace Wbc\UtilityBundle;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpKernel\KernelInterface;
use Twilio\Rest\Client;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;

/**
 * Class TwilioManager.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @DI\Service("wbc.utility.twilio_manager")
 */
class TwilioManager
{
    /**
     * @var string
     */
    private $fromNumber;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var PhoneNumberUtil
     */
    private $phoneUtil;

    /**
     * @var string
     */
    private $env;

    /**
     * TwilioManager Constructor.
     *
     * @DI\InjectParams({
     * "sid" = @DI\Inject("%twilio_sid%"),
     * "token" = @DI\Inject("%twilio_token%"),
     * "fromNumber" = @DI\Inject("%twilio_from_number%"),
     * "kernel" = @DI\Inject("kernel")
     * })
     *
     * @param $sid
     * @param $token
     * @param $fromNumber
     * @param KernelInterface $kernel
     */
    public function __construct($sid, $token, $fromNumber, KernelInterface $kernel)
    {
        $this->fromNumber = $fromNumber;
        $this->client = new Client($sid, $token);
        $this->phoneUtil = PhoneNumberUtil::getInstance();
        $this->env = $kernel->getEnvironment();
    }

    /**
     * Sends an SMS message.
     *
     * @param $toPhoneNumber
     * @param $message
     *
     * @return \Twilio\Rest\Api\V2010\Account\MessageInstance
     */
    public function sendSms($toPhoneNumber, $message)
    {
        $response = null;

        if ($this->env !== 'prod') {
            return $response;
        }

        try {
            $phoneNumberProto = $this->phoneUtil->parse($toPhoneNumber, 'AE');
            $phoneNumber = $this->phoneUtil->format($phoneNumberProto, PhoneNumberFormat::INTERNATIONAL);

            $response = $this->client->messages->create($phoneNumber, ['from' => $this->fromNumber, 'body' => $message]);
        } catch (NumberParseException $e) {
            //ignore
        }

        return $response;
    }
}
