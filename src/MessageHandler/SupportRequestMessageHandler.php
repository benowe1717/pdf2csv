<?php

namespace App\MessageHandler;

use App\Message\SupportRequestMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SupportRequestMessageHandler
{
    public function __invoke(SupportRequestMessage $message): void
    {
        // do something with your message
    }
}
