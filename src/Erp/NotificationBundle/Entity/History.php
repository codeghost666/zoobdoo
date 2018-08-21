<?php

namespace Erp\NotificationBundle\Entity;

use Erp\CoreBundle\Entity\DatesAwareTrait;
use Erp\CoreBundle\Entity\DatesAwareInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class History
 *
 * @ORM\Table(name="erp_notification_history")
 * @ORM\Entity(repositoryClass="Erp\NotificationBundle\Repository\HistoryRepository")
 * @ORM\HasLifecycleCallbacks
 */
class History implements DatesAwareInterface
{
    use DatesAwareTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="tenantName", type="string")
     */
    private $tenantName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="received_at", type="datetime", nullable=true)
     */
    protected $receivedAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_send_alert_automatically", type="integer")
     */
    private $sendAlertAutomatically;

    /**
     * @var Erp\PropertyBundle\Entity\Property
     *
     * @ORM\ManyToOne(targetEntity="Erp\PropertyBundle\Entity\Property")
     * @ORM\JoinColumn(name="property_id", referencedColumnName="id")
     */
    private $property;

    public static function createFromArray(array $fields) : History
    {
        return new self($fields);
    }

    public function __construct(array $fields)
    {
        $this->type = $fields['type'];
        $this->title = $fields['title'];
        $this->tenantName = $fields['tenantName'];
        $this->receivedAt = $fields['receivedAt'] ?? null;
        $this->sendAlertAutomatically = false;
        $this->property = $fields['property'] ?? null;
    }

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
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedDate()
    {
        $this->updatedAt = new \DateTime();
    }

    public function markAsReceived() : History
    {
        $this->receivedAt = new \DateTime();
        return $this;
    }

    public function getType() : string
    {
        return $this->type;
    }

    public function getTitle() : string
    {
        return $this->title;
    }

    public function getTenantName() : string
    {
        return $this->tenantName;
    }

    public function getReceivedAt()
    {
        return $this->receivedAt;
    }

    public function isSendAlertAutomatically() : bool
    {
        return $this->sendAlertAutomatically === true;
    }

    public function getProperty()
    {
        return $this->property;
    }
}
