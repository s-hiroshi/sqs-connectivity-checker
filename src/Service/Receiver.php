<?php


namespace Quartetcom\SQSConnectivityChecker\Service;


use Aws\Exception\AwsException;
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
    public function receiveMessage()
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
                $result = $this->client->deleteMessage([
                    'QueueUrl' => getenv('QUEUE_URL'),
                    'ReceiptHandle' => $result->get('Messages')[0]['ReceiptHandle']
                ]);
                return 0;
            } else {
                return 0;
            }
        } catch (AwsException $e) {
            throw new \Exception($e->getMessage());
        }
    }

}