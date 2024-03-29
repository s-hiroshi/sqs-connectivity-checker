<?php


namespace HSawai\SQSConnectivityChecker\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use HSawai\SQSConnectivityChecker\Service\Sender;

class SenderCommand extends Command
{

   const NAME = 'sqs:send';
   private $sender;

    public function __construct(Sender $sender)
    {
        parent::__construct(self::NAME);
        $this->sender = $sender;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        try {
            $result = $this->sender->send();
            $output->writeln($result->__toString());

            return 0;
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());

            return 255;
        }
    }
}