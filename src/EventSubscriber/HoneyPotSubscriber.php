<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HoneyPotSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;
    
    private RequestStack $requestStack;

    public function __construct(LoggerInterface $logger, RequestStack $requestStack)
    {
        $this->logger = $logger;
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents(): array {

        return [
            FormEvents::PRE_SUBMIT => 'checkHoneyJar'
        ];
    }

    public function checkHoneyJar(FormEvent $event): void {

        $request = $this->requestStack->getCurrentRequest();

        if(!$request) {
            return;
        }

        $form = $event->getForm();
        $data = $event->getData();

        if(!array_key_exists('phone', $data) || !array_key_exists('raison', $data)) {
            throw new HttpException(400, 'Go away to my form');
        }

        [
            'phone' => $phone,
            'raison' => $raison
        ] = $data;

        if($phone !== '' || $raison !== '') {
            $this->logger->error("Potentielle tentative de spam d\'un robot, à l\'adresse ip suivante :'{$request->getClientIp()}'");
            $this->logger->info("Les données du champ phone contenait '{$phone}', et le champ raison contenait '{$raison}'");
            
            throw new HttpException(403, 'Go away to my form, bot !');
        }
    }
}