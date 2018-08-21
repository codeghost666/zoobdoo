<?php

namespace Erp\PaymentBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use \Doctrine\ORM\EntityManager;

class MakeOnePaymentCommand extends ContainerAwareCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('erp_payment:make_one')
            ->setDescription('Charge Pay Simple Tenants');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('erp.payment.paysimple_service')->chargeTenants();
    }
}
