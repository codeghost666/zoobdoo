<?php

namespace Erp\WorkorderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Service
 *
 * @ORM\Table(name="workorder_service")
 * @ORM\Entity(repositoryClass="Erp\WorkorderBundle\Entity\ServiceRepository")
 */
class Service
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="task_name", type="string", length=255)
     */
    private $taskName;

    /**
     * @var integer
     *
     * @ORM\Column(name="hours", type="integer")
     */
    private $hours;

    /**
     * @var integer
     *
     * @ORM\Column(name="rate", type="integer")
     */
    private $rate;

    /**
     * @var string
     *
     * @ORM\Column(name="tax_code", type="string", length=255)
     */
    private $taxCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="workorder_id", type="integer")
     */
    private $workorderId;

    /**
     * @var integer
     *
     * @ORM\Column(name="actions", type="integer")
     */
    private $actions;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set taskName
     *
     * @param string $taskName
     *
     * @return Service
     */
    public function setTaskName($taskName)
    {
        $this->taskName = $taskName;

        return $this;
    }

    /**
     * Get taskName
     *
     * @return string
     */
    public function getTaskName()
    {
        return $this->taskName;
    }

    /**
     * Set hours
     *
     * @param integer $hours
     *
     * @return Service
     */
    public function setHours($hours)
    {
        $this->hours = $hours;

        return $this;
    }

    /**
     * Get hours
     *
     * @return integer
     */
    public function getHours()
    {
        return $this->hours;
    }

    /**
     * Set rate
     *
     * @param integer $rate
     *
     * @return Service
     */
    public function setRate($rate)
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * Get rate
     *
     * @return integer
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * Set taxCode
     *
     * @param string $taxCode
     *
     * @return Service
     */
    public function setTaxCode($taxCode)
    {
        $this->taxCode = $taxCode;

        return $this;
    }

    /**
     * Get taxCode
     *
     * @return string
     */
    public function getTaxCode()
    {
        return $this->taxCode;
    }

    /**
     * Set workorderId
     *
     * @param integer $workorderId
     *
     * @return Service
     */
    public function setWorkorderId($workorderId)
    {
        $this->workorderId = $workorderId;

        return $this;
    }

    /**
     * Get workorderId
     *
     * @return integer
     */
    public function getWorkorderId()
    {
        return $this->workorderId;
    }

    /**
     * Set actions
     *
     * @param integer $actions
     *
     * @return Service
     */
    public function setActions($actions)
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * Get actions
     *
     * @return integer
     */
    public function getActions()
    {
        return $this->actions;
    }
}

