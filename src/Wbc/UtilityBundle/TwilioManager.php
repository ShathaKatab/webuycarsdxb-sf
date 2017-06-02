<?php

namespace Wbc\UtilityBundle;

use JMS\DiExtraBundle\Annotation as DI;
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

    private $phoneUtil;

    /**
     * TwilioManager Constructor.
     *
     * @DI\InjectParams({
     * "sid" = @DI\Inject("%twilio_sid%"),
     * "token" = @DI\Inject("%twilio_token%"),
     * "fromNumber" = @DI\Inject("%twilio_from_number%")
     * })
     *
     * @param $sid
     * @param $token
     * @param $fromNumber
     */
    public function __construct($sid, $token, $fromNumber)
    {
        $this->fromNumber = $fromNumber;
        $this->client = new Client($sid, $token);
        $this->phoneUtil = PhoneNumberUtil::getInstance();
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
