<?php

declare(strict_types=1);

namespace Wbc\UtilityBundle\EventListener;

use Noxlogic\RateLimitBundle\Events\GenerateKeyEvent;

/**
 * Class IpBasedRateLimitGenerateKeyListener.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class IpBasedRateLimitGenerateKeyListener
{
    /**
     * onGenerateKey.
     *
     * @param GenerateKeyEvent $event
     */
    public function onGenerateKey(GenerateKeyEvent $event): void
    {
        $request = $event->getRequest();
        $event->addToKey($request->getClientIp());
    }
}
