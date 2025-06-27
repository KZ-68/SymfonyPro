<?php

namespace App\Message\MessageHandler;

use App\Message\SampleMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SampleMessageHandler
{
    public function __invoke(SampleMessage $message)
    {
        print_r('Handler handled the message!');
    }
}