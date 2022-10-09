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
    public function send(): Result
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
                    'StringValue' => getenv('MESSAGE_ATTRIBUTES_TITLE'),
                ],
            ],
            'MessageBody' => getenv('MESSAGE_BODY'),
            'QueueUrl' => getenv('QUEUE_URL'),
        ];
    }
}