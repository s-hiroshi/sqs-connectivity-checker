<?php

namespace Quartetcom\SQSConnectivityChecker\Service;
use Aws\Sqs\SqsClient;
use Aws\Exception\AwsException;
class Sender
{
    private $client; 

    public function __construct(SqsClient $client)
    {
        $this->client = $client;
    }

    /**
     * @throws \Exception
     */
    public function sendMessage(string $messageBody) 
    {
        $message = $this->createParameter($messageBody);
        try {
            return $this->client->sendMessage($message);
        } catch (AwsException $e) {
            throw new \Exception($e->getMessage());
        } 
    }
    
    private function createParameter(string $messageBody = 'Hello World'): array {
        return [
            'DelaySeconds' => 10,
            'MessageAttributes' => [
                "Title" => [
                    'DataType' => "String",
                    'StringValue' => "SQS Connectivity Checker"
                ],
            ],
            'MessageBody' => $messageBody,
            'QueueUrl' => getenv('QUEUE_URL')
        ];
    }

}