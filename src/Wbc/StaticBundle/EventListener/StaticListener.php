<?php

namespace Wbc\StaticBundle\EventListener;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class StaticListener.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @DI\Service()
 */
class StaticListener
{
    const UTM_SOURCE = 'utm_source';

    /**
     * @DI\Observe(KernelEvents::REQUEST)
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            // don't do anything if it's not the master request
            return;
        }

        $request = $event->getRequest();
        $utmSource = $request->get('utm_source');

        if(!$utmSource){
            return;
        }

        $session = $request->getSession();
        $session->set(self::UTM_SOURCE, $utmSource);
    }
}
