<?php

namespace HSawai\SQSConnectivityChecker\Tests\Command;

use Aws\Exception\AwsException;
use Aws\Result;
use Aws\Sqs\SqsClient;
use Aws\MockHandler;
use Aws\CommandInterface;
use Exception;
use HSawai\SQSConnectivityChecker\Command\ReceiverCommand;
use HSawai\SQSConnectivityChecker\Service\Receiver;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\Console\Tester\CommandTester;

class ReceiverCommandTest extends TestCase
{
    private $client;
    private $mockHandler;

    protected function setUp(): void
    {
        $this->mockHandler = new MockHandler();
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

        $this->mockHandler->append(new Result($receiverResultContent)); // Call SqsClient::receiveMessage
        $this->mockHandler->append(new Result()); // Call SqsClient::deleteMessage
        // Call SqsClient::receiveMessage with AwsException
        $this->mockHandler->append(function (CommandInterface $cmd, RequestInterface $req) {
            return new AwsException('Mock exception', $cmd);
        });
        $this->client = new SqsClient([
            'region' => 'ap-northeast-1',
            'version' => '2012-11-05',
            'handler' => $this->mockHandler,
            'credentials' => false,
        ]);
    }

    public function testReceiveCommandSuccess(): void
    {
        $receiver = new Receiver($this->client);

        $command = new ReceiverCommand($receiver);
        $commandTester = new CommandTester($command);
        $actual = $commandTester->execute([]);

        $this->assertEquals(0, $actual);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testReceiverCommandFailed(): void
    {
        $this->expectException(Exception::class);

        $receiver = new Receiver($this->client);
        $receiver->receive();
        $receiver->receive();

        $command = new ReceiverCommand($receiver);
        $commandTester = new CommandTester($command);
        $actual = $commandTester->execute([]);

        $this->assertEquals(255, $actual);

    }
}
