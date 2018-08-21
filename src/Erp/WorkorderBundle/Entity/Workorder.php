<?php

namespace Erp\WorkorderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Workorder
 *
 * @ORM\Table(name="workorder")
 * @ORM\Entity(repositoryClass="Erp\WorkorderBundle\Repository\WorkorderRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Workorder
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
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime")
     */
    private $createdDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="item_begin_service", type="string", length=255)
     */
    private $service;

    /**
     * @var integer
     *
     * @ORM\Column(name="contractor_id", type="integer")
     */
    private $contractor;

    /**
     * @var integer
     *
     * @ORM\Column(name="manager_id", type="integer")
     */
    private $manager;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=255)
     */
    private $currency;

    /**
     * @var string
     *
     * @ORM\Column(name="severity", type="string", length=255)
     */
    private $severity;

    /**
     * @var string
     *
     * @ORM\Column(name="urgency", type="string", length=255)
     */
    private $urgency;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=512)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="service_date", type="datetime")
     */
    private $serviceDate;

    /**
     * @var string
     *
     * @ORM\Column(name="service_time", type="string", length=255)
     */
    private $serviceTime;


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
     * Set createdDate
     * @ORM\PrePersist
     * @param \DateTime $createdDate
     *
     * @return Workorder
     */
    public function setCreatedDate($createdDate)
    {
        /*if (!isset($this->createdDate))
            $this->createdDate = DateTime();*/
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Workorder
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set service
     *
     * @param string $service
     *
     * @return Workorder
     */
    public function setService($service)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Get service
     *
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set contractor
     *
     * @param integer $contractor
     *
     * @return Workorder
     */
    public function setContractor($contractor)
    {
        $this->contractor = $contractor;

        return $this;
    }

    /**
     * Get contractor
     *
     * @return integer
     */
    public function getContractor()
    {
        return $this->contractor;
    }

    /**
     * Set manager
     *
     * @param integer $manager
     *
     * @return Workorder
     */
    public function setManager($manager)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * Get manager
     *
     * @return integer
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Set currency
     *
     * @param string $currency
     *
     * @return Workorder
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set severity
     *
     * @param string $severity
     *
     * @return Workorder
     */
    public function setSeverity($severity)
    {
        $this->severity = $severity;

        return $this;
    }

    /**
     * Get severity
     *
     * @return string
     */
    public function getSeverity()
    {
        return $this->severity;
    }

    /**
     * Set urgency
     *
     * @param string $urgency
     *
     * @return Workorder
     */
    public function setUrgency($urgency)
    {
        $this->urgency = $urgency;

        return $this;
    }

    /**
     * Get urgency
     *
     * @return string
     */
    public function getUrgency()
    {
        return $this->urgency;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Workorder
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set serviceDate
     *
     * @param \DateTime $serviceDate
     *
     * @return Workorder
     */
    public function setServiceDate($serviceDate)
    {
        $this->serviceDate = $serviceDate;

        return $this;
    }

    /**
     * Get serviceDate
     *
     * @return \DateTime
     */
    public function getServiceDate()
    {
        return $this->serviceDate;
    }

    /**
     * Set serviceTime
     *
     * @param string $serviceTime
     *
     * @return Workorder
     */
    public function setServiceTime($serviceTime)
    {
        $this->serviceTime = $serviceTime;

        return $this;
    }

    /**
     * Get serviceTime
     *
     * @return string
     */
    public function getServiceTime()
    {
        return $this->serviceTime;
    }
}

