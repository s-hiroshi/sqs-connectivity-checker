<?php


namespace VSC\Messenger\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VSC\Messenger\Message\SmsNotification;
use VSC\Messenger\MessageHandler\SmsNotificationHandler;
use VSC\Messenger\Service\MessageGenerator;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;


class MessageCommand extends Command
{

   const NAME = 'command:message';
   private $messageGenerator;
   private $bus;

    public function __construct(MessageGenerator $messageGenerator)
    {
        parent::__construct(self::NAME);
        $this->messageGenerator = $messageGenerator;
    }

    protected function configure()
    {
        $this->addUsage('挨拶を表示');

        $this->addArgument(
            'message',
            InputArgument::REQUIRED,
            'Message for display'
        );

        $this->addOption(
            'name',
            null,
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'first name, last name',
            ['foo']
        );

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        var_dump(getenv('MESSENGER_TRANSPORT_DSN'));
        $handler = new SmsNotificationHandler();

        $bus = new MessageBus([
            new HandleMessageMiddleware(new HandlersLocator([
                SmsNotification::class => [$handler],
            ])),
        ]);
        $bus->dispatch(new SmsNotification('Look! I created a message!'));

        $message = $input->getArgument('message');
        $name = $input->getOption('name');
        $output->writeln($this->messageGenerator->getMessage($message, $name));


        return 0;
    }
}