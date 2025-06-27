<?php

namespace App\Controller\Message;

use App\Message\SampleMessage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SampleMessageController extends AbstractController
{
    #[Route('/sample', name: 'sample')]
    public function sample(MessageBusInterface $bus): Response
    {
        $message = new SampleMessage('content');
        $bus->dispatch($message);

        return new Response(sprintf('Message with content %s was published', $message->getContent()));
    }
}