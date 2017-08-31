<?php

namespace fiunchinho\Silex\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use OldSound\RabbitMqBundle\Command\BaseConsumerCommand;

class Consumer extends BaseConsumerCommand
{
    protected function configure()
    {
        parent::configure();
        $this->setDescription('Executes a consumer');
    }

    protected function getConsumerService()
    {
        return '%s';
    }

    protected function initConsumer($input)
    {
        $this->consumer = $this->getContainer()
            ->get('rabbit.consumer')[sprintf($this->getConsumerService(), $input->getArgument('name'))];

        if (!is_null($input->getOption('memory-limit')) && ctype_digit((string) $input->getOption('memory-limit')) && $input->getOption('memory-limit') > 0) {
            $this->consumer->setMemoryLimit($input->getOption('memory-limit'));
        }
        $this->consumer->setRoutingKey($input->getOption('route'));
    }
}
