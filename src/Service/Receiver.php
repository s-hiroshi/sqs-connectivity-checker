<?php


namespace Quartetcom\SQSConnectivityChecker\Service;


use Aws\Exception\AwsException;
use Aws\Result;
use Aws\Sqs\SqsClient;


class Receiver
{
    private $client;

    public function __construct(SqsClient $client)
    {
        $this->client = $client;
    }

    /**
     * @throws \Exception
     */
    public function receiveMessage(): Result
    {
        try {
            $result = $this->client->receiveMessage(array(
                'AttributeNames' => ['SentTimestamp'],
                'MaxNumberOfMessages' => 1,
                'MessageAttributeNames' => ['All'],
                'QueueUrl' => getenv('QUEUE_URL'),
                'WaitTimeSeconds' => 0,
            ));
            if (!empty($result->get('Messages'))) {
                return $this->client->deleteMessage([
                    'QueueUrl' => getenv('QUEUE_URL'),
                    'ReceiptHandle' => $result->get('Messages')[0]['ReceiptHandle'],
                ]);
            } else {
                return $result;
            }
        } catch (AwsException $e) {
            throw new \Exception($e->getMessage());
        }
    }

}