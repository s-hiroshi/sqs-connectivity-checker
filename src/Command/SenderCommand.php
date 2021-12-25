<?php


namespace Quartetcom\SQSConnectivityChecker\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Quartetcom\SQSConnectivityChecker\Service\Sender;

class SenderCommand extends Command
{

   const NAME = 'sqs:sender';
   private $sender;

    public function __construct(Sender $sender)
    {
        parent::__construct(self::NAME);
        $this->sender = $sender;
    }

    protected function configure()
    {
        $this->addUsage('Send message to sqs');

        $this->addArgument(
            'messageBody',
            InputArgument::REQUIRED,
            'Message body to send to SQS'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $messageBody = $input->getArgument('messageBody');
        try {
            $this->sender->sendMessage($messageBody);

            return 0;
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());

            return 255;
        }
    }
}