<?php

namespace Erp\PaymentBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use \Doctrine\ORM\EntityManager;

class RecurringPaymentCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('erp_payment:check_recurring')
            ->setDescription('Check Pay Simple Customers recurring');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('erp.payment.paysimple_service')->checkRecurring();
    }
}
