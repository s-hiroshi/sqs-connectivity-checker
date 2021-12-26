<?php


namespace Quartetcom\SQSConnectivityChecker\Command;

use Quartetcom\SQSConnectivityChecker\Service\Receiver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReceiverCommand extends Command
{

    const NAME = 'sqs:receive';
    private $receiver;

    public function __construct(Receiver $receiver)
    {
        parent::__construct(self::NAME);
        $this->receiver = $receiver;
    }

    protected function configure()
    {
        $this->addUsage('Receive message to sqs');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $result = $this->receiver->receiveMessage();
            var_dump(get_class($result));
            $output->writeln($result->__toString());

            return 0;
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());

            return 255;
        }
    }
}