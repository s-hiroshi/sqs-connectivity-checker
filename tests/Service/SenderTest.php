<?php

namespace HSawai\SQSConnectivityChecker\Tests;

use Aws\Exception\AwsException;
use Aws\Command;
use Aws\Result;
use Aws\Sqs\SqsClient;
use Exception;
use HSawai\SQSConnectivityChecker\Service\Sender;

use PHPUnit\Framework\TestCase;

class SenderTest extends TestCase
{
    private $sqsClient;



    protected function setUp(): void
    {
        $this->sqsClient = $this->getMockBuilder(SqsClient::class)
            ->disableOriginalConstructor()
            ->addMethods(['sendMessage'])
            ->getMock();
    }

    public function testSendMessageSuccess(): void
    {
        $this->sqsClient->expects($this->once())
            ->method('sendMessage')
            ->willReturn(new Result(['foo' => 'bar']));
        $sender = new Sender($this->sqsClient);

        $this->assertInstanceOf(Result::class, $sender->sendMessage());
    }

    public function testSendMessageException(): void
    {

        $command = $this->createMock(Command::class);
        $this->sqsClient->expects($this->once())
            ->method('sendMessage')
            ->willThrowException(new AwsException('SOME ERROR', $command));
        $sender = new Sender($this->sqsClient);

        $this->expectException(Exception::class);
        $sender->sendMessage();
    }
}
