<?php

namespace Erp\PaymentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Erp\UserBundle\Entity\User;
use Erp\PaymentBundle\Entity\PaySimpleRecurringPayment;

/**
 * PaySimpleCustomer
 *
 * @ORM\Table(name="ps_customers", indexes={@ORM\Index(name="customer_idx", columns={"customer_id"})})
 * @ORM\Entity(repositoryClass="Erp\PaymentBundle\Repository\PaySimpleCustomerRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class PaySimpleCustomer
{
    const PRIMARY_TYPE_CC = 'cc';
    const PRIMARY_TYPE_BA = 'ba';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="\Erp\UserBundle\Entity\User", inversedBy="paySimpleCustomers")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var int
     *
     * @ORM\Column(name="customer_id", type="integer")
     */
    protected $customerId;

    /**
     * @var int
     *
     * @ORM\Column(name="cc_id", type="integer", nullable=true)
     */
    protected $ccId;

    /**
     * @var int
     *
     * @ORM\Column(name="ba_id", type="integer", nullable=true)
     */
    protected $baId;

    /**
     * @var string
     *
     * @ORM\Column(
     *      name="primary_type",
     *      length=2,
     *      type="string",
     *      nullable=true
     * )
     */
    protected $primaryType;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime")
     */
    protected $createdDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_date", type="datetime")
     */
    protected $updatedDate;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Erp\PaymentBundle\Entity\PaySimpleRecurringPayment",
     *                mappedBy="paySimpleCustomer",
     *                cascade={"ALL"}
     * )
     * @ORM\OrderBy({"createdDate"="DESC"})
     */
    protected $psRecurringPayments;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Erp\PaymentBundle\Entity\PaySimpleDeferredPayments",
     *                mappedBy="paySimpleCustomer",
     *                cascade={"ALL"},
     *                    orphanRemoval=true
     * )
     * @ORM\OrderBy({"updatedDate"="DESC"})
     */
    protected $psDeferredPayments;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->psRecurringPayments = new ArrayCollection();
        $this->psDeferredPayments = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set customerId
     *
     * @param int $customerId
     *
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;

        return $this;
    }

    /**
     * Get customerId
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * Set ccId
     *
     * @param int $ccId
     *
     * @return $this
     */
    public function setCcId($ccId)
    {
        $this->ccId = $ccId;

        return $this;
    }

    /**
     * Get ccId
     *
     * @return int
     */
    public function getCcId()
    {
        return $this->ccId;
    }

    /**
     * Set baId
     *
     * @param int $baId
     *
     * @return $this
     */
    public function setBaId($baId)
    {
        $this->baId = $baId;

        return $this;
    }

    /**
     * Get baId
     *
     * @return int
     */
    public function getBaId()
    {
        return $this->baId;
    }

    /**
     * Set primaryType
     *
     * @param string $primaryType
     *
     * @return $this
     */
    public function setPrimaryType($primaryType)
    {
        $this->primaryType = $primaryType;

        return $this;
    }

    /**
     * Get primaryType
     *
     * @return string
     */
    public function getPrimaryType()
    {
        return $this->primaryType;
    }

    /**
     * Set createdDate
     *
     * @ORM\PrePersist
     */
    public function setCreatedDate()
    {
        $this->createdDate = new \DateTime();
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
     * Set updatedDate
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setUpdatedDate()
    {
        $this->updatedDate = new \DateTime();
    }

    /**
     * Get updatedDate
     *
     * @return \DateTime
     */
    public function getUpdatedDate()
    {
        return $this->updatedDate;
    }

    /**
     * Add psRecurringPayment
     *
     * @param PaySimpleRecurringPayment $psRecurringPayment
     *
     * @return $this
     */
    public function addPSRecurringPayment(PaySimpleRecurringPayment $psRecurringPayment)
    {
        $this->psRecurringPayments[] = $psRecurringPayment;

        return $this;
    }

    /**
     * Remove psRecurringPayment
     *
     * @param PaySimpleRecurringPayment $psRecurringPayment
     */
    public function removePSRecurringPayment(PaySimpleRecurringPayment $psRecurringPayment)
    {
        $this->psRecurringPayments->removeElement($psRecurringPayment);
    }

    /**
     * Get psRecurringPayments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPsRecurringPayments()
    {
        return $this->psRecurringPayments;
    }

    /**
     * Add psDeferredPayment
     *
     * @param PaySimpleDeferredPayments $psDeferredPayment
     *
     * @return $this
     */
    public function addPSDeferredPayment(PaySimpleDeferredPayments $psDeferredPayment)
    {
        $this->psDeferredPayments[] = $psDeferredPayment;

        return $this;
    }

    /**
     * Remove psDeferredPayment
     *
     * @param PaySimpleDeferredPayments $psDeferredPayment
     */
    public function removePSDeferredPayment(PaySimpleDeferredPayments $psDeferredPayment)
    {
        $this->psDeferredPayments->removeElement($psDeferredPayment);
    }

    /**
     * Get psDeferredPayments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPSDeferredPayments()
    {
        return $this->psDeferredPayments;
    }
}
