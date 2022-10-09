<?php

namespace HSawai\SQSConnectivityChecker\Tests\Command;

use Aws\Exception\AwsException;
use Aws\Result;
use Aws\Sqs\SqsClient;
use Aws\MockHandler;
use Aws\CommandInterface;
use Exception;
use HSawai\SQSConnectivityChecker\Command\SenderCommand;
use HSawai\SQSConnectivityChecker\Service\Sender;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\Console\Tester\CommandTester;

class SenderCommandTest extends TestCase
{
    private $client;
    private $mockHandler;

    protected function setUp(): void
    {
        $this->mockHandler = new MockHandler();
        $this->mockHandler->append(new Result(['foo' => 'bar'])); // Call SqsClient::send
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
        $sender = new Sender($this->client);

        $command = new SenderCommand($sender);
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
        $sender = new Sender($this->client);
        $sender->send();

        $command = new SenderCommand($sender);
        $commandTester = new CommandTester($command);
        $actual = $commandTester->execute([]);

        $this->assertEquals(255, $actual);

    }
}
