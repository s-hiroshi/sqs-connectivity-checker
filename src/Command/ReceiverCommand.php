<?php


namespace Quartetcom\SQSConnectivityChecker\Command;

use Quartetcom\SQSConnectivityChecker\Service\Receiver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReceiverCommand extends Command
{

    const NAME = 'sqs:receive';
    private Receiver $receiver;

    public function __construct(Receiver $receiver)
    {
        parent::__construct(self::NAME);
        $this->receiver = $receiver;
    }

    protected function configure()
    {
        $this->addUsage('Receive message to sqs');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $result = $this->receiver->receive();
            $output->writeln($result);

            return 0;
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());

            return 255;
        }
    }
}