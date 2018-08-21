<?php

namespace Erp\PropertyBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Erp\PropertyBundle\Entity\Property;

class NotifyUsersBeforePaymentCommand extends ContainerAwareCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('erp:property:notify-users-before-payment')
            ->setDescription('Notify users before payment');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }

    private function notifyBeforeOneDay()
    {
        $repository = $this->getRepository();

    }

    private function notifyBeforeThredays()
    {

    }

    private function getRepository()
    {
        $container = $this->getContainer();
        $repository = $container->get('doctrine')->getManagerForClass(Property::class)->getRepository(Property::class);

        return $repository;
    }
}