<?php
namespace VSC\Messenger\Service;

class MessageGenerator {

    public function getMessage(string $message, array $name): string
    {
        return sprintf('%s！ %sさん', $message, implode(' ', $name));
    }

}