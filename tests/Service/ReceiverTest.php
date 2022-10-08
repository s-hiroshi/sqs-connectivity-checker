<?php

namespace HSawai\SQSConnectivityChecker\Tests\Service;

use Aws\Exception\AwsException;
use Aws\Result;
use Aws\Sqs\SqsClient;
use Exception;
use HSawai\SQSConnectivityChecker\Service\Receiver;
use Aws\MockHandler;
use Aws\CommandInterface;
use Psr\Http\Message\RequestInterface;

use PHPUnit\Framework\TestCase;

class ReceiverTest extends TestCase
{
    private $client;
    private $mock;

    protected function setUp(): void
    {
        $this->mock = new MockHandler();

        $this->client = new SqsClient([
            'region' => 'ap-northeast-1',
            'version' => '2012-11-05',
            'handler' => $this->mock,
            'credentials' => false,
        ]);
    }

    public function testReceiveMessageSuccess(): void
    {
        $receiverResultContent = [
            "Messages" => [
                [
                    "MessageId" => "MessageId",
                    "ReceiptHandle" => "ReceiptHandle",
                    "MD5OfBody" => "ac101b32dda4448cf13a93fe283dddd8",
                    "Body" => "Body",
                    "Attributes" => [
                        "SentTimestamp" => "0",
                    ],
                ],
            ],
        ];

        $this->mock->append(new Result($receiverResultContent));
        $this->mock->append(new Result());
        $receiver = new Receiver($this->client);

        $this->assertInstanceOf(Result::class, $receiver->receive()[0]);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testReceiverMessageException(): void
    {
        $this->expectException(Exception::class);
        $this->mock->append(function (CommandInterface $cmd, RequestInterface $req) {
            return new AwsException('Mock exception', $cmd);
        });

        $receiver = new Receiver($this->client);
        $receiver->receive();
        $receiver->receive();
    }
}
