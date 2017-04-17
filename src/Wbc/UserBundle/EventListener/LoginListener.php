<?php

namespace Wbc\UserBundle\EventListener;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * Class LoginListener.
 *
 * @DI\Service()
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class LoginListener
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    /**
     * @DI\InjectParams({
     * "router" = @DI\Inject("router"),
     * "authorizationChecker" = @DI\Inject("security.authorization_checker")
     *
     * })
     *
     * @param Router               $router
     * @param AuthorizationChecker $authorizationChecker
     */
    public function __construct(Router $router, AuthorizationChecker $authorizationChecker)
    {
        $this->router = $router;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @DI\Observe(SecurityEvents::INTERACTIVE_LOGIN)
     *
     * @param InteractiveLoginEvent $event
     *
     * @return RedirectResponse
     */
    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        if ($this->authorizationChecker->isGranted('ROLE_BUYER')) {
            return new RedirectResponse($this->router->generate('sonata_admin_dashboard'));
        }
    }
}
