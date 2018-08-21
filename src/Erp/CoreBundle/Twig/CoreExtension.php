<?php

namespace Erp\CoreBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CoreExtension
 *
 * @package Erp\UserBundle\Twig
 */
class CoreExtension extends \Twig_Extension
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'core_extension';
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            'json_decode' => new \Twig_Filter_Method($this, 'jsonDecode'),
            'money' => new \Twig_Filter_Method($this, 'formatMoney'),
            'internal_type' => new \Twig_Filter_Method($this, 'formatInternalType'),
            'transaction_status' => new \Twig_Filter_Method($this, 'formatTransactionStatus'),
        ];
    }

    /**
     * Decode string to array
     *
     * @param $str
     *
     * @return array
     */
    public function jsonDecode($str)
    {
        return json_decode($str, true);
    }

    public function formatMoney($value)
    {
        if (null === $value || '' === $value) {
            return $value;
        }

        $formatter = $this->container->get('erp_core.formatter.money_formatter');

        return $formatter->format($value);
    }

    public function formatInternalType($value)
    {
        $formatter = $this->container->get('erp_core.formatter.internal_type_formatter');

        return $formatter->format($value);
    }

    public function formatTransactionStatus($value)
    {
        $formatter = $this->container->get('erp_core.formatter.transaction_status_formatter');

        return $formatter->format($value);
    }
}
