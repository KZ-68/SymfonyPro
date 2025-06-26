<?php

namespace App\EventListener;

use HWI\Bundle\OAuthBundle\Event\FormEvent;
use HWI\Bundle\OAuthBundle\HWIOAuthEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class OAuthRegistrationRedirectListener implements EventSubscriberInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            HWIOAuthEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
        ];
    }

    public function onRegistrationSuccess(FormEvent $event): void
    {
        $url = $this->router->generate('registration_success');
        $event->setResponse(new RedirectResponse($url));
    }
}