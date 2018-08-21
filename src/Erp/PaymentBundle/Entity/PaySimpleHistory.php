<?php

namespace Erp\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Erp\PropertyBundle\Entity\Property;
use Symfony\Component\Validator\Constraints as Assert;
use Erp\UserBundle\Entity\User;

/**
 * PaySimpleHistory
 *
 * @ORM\Table(name="ps_history")
 * @ORM\Entity(repositoryClass="Erp\PaymentBundle\Repository\PaySimpleHistoryRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class PaySimpleHistory
{
    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR   = 'error';
    const STATUS_PENDING = 'pending';

    const EXPORT_PDF = 'pdf';
    const EXPORT_CSV = 'csv';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="\Erp\UserBundle\Entity\User", inversedBy="paySimpleHistory")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var Property
     *
     * @ORM\ManyToOne(targetEntity="\Erp\PropertyBundle\Entity\Property", inversedBy="paySimpleHistories")
     * @ORM\JoinColumn(name="property_id", referencedColumnName="id", nullable=false)
     */
    protected $property;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="paymentDate", type="datetime")
     */
    protected $paymentDate;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    protected $amount;

    /**
     * @var string
     *
     * @ORM\Column(
     *      name="status",
     *      type="string",
     *      length=16,
     *      nullable=true
     * )
     */
    protected $status = self::STATUS_SUCCESS;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    protected $notes;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="transfer_date", type="datetime")
     */
    protected $transferDate;

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
     * Get id
     *
     * @return integer
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
     * Set property
     *
     * @param Property $property
     *
     * @return $this
     */
    public function setProperty(Property $property)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get property
     *
     * @return Property
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Set paymentDate
     *
     * @param \DateTime $paymentDate
     *
     * @return $this
     */
    public function setPaymentDate($paymentDate)
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    /**
     * Get paymentDate
     *
     * @return \DateTime
     */
    public function getPaymentDate()
    {
        return $this->paymentDate;
    }

    /**
     * Set amount
     *
     * @param float $amount
     *
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set notes
     *
     * @param string $notes
     *
     * @return $this
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set transferDate
     *
     * @param \DateTime $transferDate
     *
     * @return $this
     */
    public function setTransferDate($transferDate)
    {
        $this->transferDate = $transferDate;

        return $this;
    }

    /**
     * Get transferDate
     *
     * @return \DateTime
     */
    public function getTransferDate()
    {
        return $this->transferDate;
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
     * Set status
     *
     * @param string $status
     *
     * @return PaySimpleHistory
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
}
