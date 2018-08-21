<?php

namespace Erp\WorkorderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Edit
 *
 * @ORM\Table(name="workorder")
 * @ORM\Entity(repositoryClass="Erp\WorkorderBundle\Entity\EditRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Edit
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
     * @var integer
     *
     * @ORM\Column(name="severity", type="integer")
     */
    private $severity;

    /**
     * @var integer
     *
     * @ORM\Column(name="urgency", type="integer")
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
     *
     * @param \DateTime $createdDate
     *
     * @return Edit
     */
    public function setCreatedDate($createdDate)
    {
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
     * @return Edit
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
     * @return Edit
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
     * @return Edit
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
     * @return Edit
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
     * @return Edit
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
     * @param integer $severity
     *
     * @return Edit
     */
    public function setSeverity($severity)
    {
        $this->severity = $severity;

        return $this;
    }

    /**
     * Get severity
     *
     * @return integer
     */
    public function getSeverity()
    {
        return $this->severity;
    }

    /**
     * Set urgency
     *
     * @param integer $urgency
     *
     * @return Edit
     */
    public function setUrgency($urgency)
    {
        $this->urgency = $urgency;

        return $this;
    }

    /**
     * Get urgency
     *
     * @return integer
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
     * @return Edit
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
     * @return Edit
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
     * @return Edit
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

