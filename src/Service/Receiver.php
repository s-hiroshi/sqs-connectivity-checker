<?php


namespace Quartetcom\SQSConnectivityChecker\Service;


use Aws\Exception\AwsException;
use Aws\Result;
use Aws\Sqs\SqsClient;
use Exception;


class Receiver
{
    private SqsClient $client;
    private string $queueUrl;

    public function __construct(SqsClient $client)
    {
        $this->client = $client;
        $this->queueUrl = getenv('QUEUE_URL');
    }

    /**
     * @throws Exception
     */
    private function deleteMessage(string $receiptHandle): Result
    {
        try {
            return $this->client->deleteMessage([
                'QueueUrl' => $this->queueUrl,
                'ReceiptHandle' => $receiptHandle,
            ]);
        } catch (AwsException $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @return \Aws\Result
     * @throws \Exception
     */
    private function receiveMessage(): Result
    {
        try {
            return $this->client->receiveMessage(array(
                'AttributeNames' => ['SentTimestamp'],
                'MaxNumberOfMessages' => 1, // 1件のみを取得
                'MessageAttributeNames' => ['All'],
                'QueueUrl' => $this->queueUrl,
                'WaitTimeSeconds' => 0,
            ));
        } catch (AwsException $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @return \Aws\Result[]|string[]
     * @throws Exception
     */
    public function receive(): array
    {
        try {
            $receiveResult = $this->receiveMessage();
            if (!empty($receiveResult[0])) {
               return [
                   $receiveResult,
                   $this->deleteMessage($receiveResult->get('Messages')[0]['ReceiptHandle'])
               ];

            }
            return[$receiveResult];
                
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}