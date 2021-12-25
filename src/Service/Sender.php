<?php

namespace HSawai\SQSConnectivityChecker\Service;

use Aws\Exception\AwsException;
use Aws\Result;
use Aws\Sqs\SqsClient;
use Exception;

class Sender
{
    private SqsClient $client;

    public function __construct(SqsClient $client)
    {
        $this->client = $client;
    }

    /**
     * @return \Aws\Result
     * @throws \Exception
     */
    public function sendMessage(): Result
    {
        $message = $this->createParameter();
        try {
            return $this->client->sendMessage($message);
        } catch (AwsException $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function createParameter(): array
    {
        return [
            'DelaySeconds' => 10,
            'MessageAttributes' => [
                "Title" => [
                    'DataType' => "String",
                    'StringValue' => "SQS Connectivity Checker",
                ],
            ],
            'MessageBody' => 'Hello World!',
            'QueueUrl' => getenv('QUEUE_URL'),
        ];
    }
}