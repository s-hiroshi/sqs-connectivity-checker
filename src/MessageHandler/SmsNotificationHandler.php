<?php
namespace VSC\Messenger\MessageHandler;

use VSC\Messenger\Message\SmsNotification;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SmsNotificationHandler implements MessageHandlerInterface
{
    public function __invoke(SmsNotification $message)
    {
        var_dump($message->getContent());
        // ... do some work - like sending an SMS message!
    }
}