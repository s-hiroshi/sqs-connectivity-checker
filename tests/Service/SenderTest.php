<?php

namespace HSawai\SQSConnectivityChecker\Tests;

use Aws\Exception\AwsException;
use Aws\Result;
use Aws\Sqs\SqsClient;
use Exception;
use HSawai\SQSConnectivityChecker\Service\Sender;
use Aws\MockHandler;
use Aws\CommandInterface;
use Psr\Http\Message\RequestInterface;

use PHPUnit\Framework\TestCase;

class SenderTest extends TestCase
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

    public function testSendMessageSuccess(): void
    {
        $this->mock->append(new Result(['foo' => 'bar']));
        $sender = new Sender($this->client);

        $this->assertInstanceOf(Result::class, $sender->sendMessage());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSendMessageException(): void
    {
        $this->expectException(Exception::class);
        $this->mock->append(function (CommandInterface $cmd, RequestInterface $req) {
            return new AwsException('Mock exception', $cmd);
        });

        $sender = new Sender($this->client);
        $sender->sendMessage();
        $sender->sendMessage();
    }
}
