<?php

namespace Erp\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Erp\UserBundle\Entity\User;

/**
 * ServiceRequest
 *
 * @ORM\Table(name="service_requests")
 * @ORM\Entity(repositoryClass="Erp\UserBundle\Repository\ServiceRequestRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ServiceRequest
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
     * @ORM\ManyToOne(
     *      targetEntity="\Erp\UserBundle\Entity\User"
     * )
     * @ORM\JoinColumn(
     *      name="from_user_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE"
     * )
     */
    protected $fromUser;

    /**
     * @ORM\ManyToOne(
     *      targetEntity="\Erp\UserBundle\Entity\User"
     * )
     * @ORM\JoinColumn(
     *      name="to_user_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE"
     * )
     */
    protected $toUser;

    /**
     * @var integer
     *
     * @ORM\Column(name="type_id", type="integer")
     *
     * @Assert\NotBlank(
     *     message="Please enter Type",
     *     groups={"ServiceRequest"}
     * )
     */
    private $typeId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     *
     * @Assert\NotBlank(
     *     message="Please enter Date",
     *     groups={"ServiceRequest"}
     * )
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     *
     * @Assert\NotBlank(
     *     message="Please enter Message",
     *     groups={"ServiceRequest"}
     * )
     *
     * @Assert\Length(
     *     min=2,
     *     max=1000,
     *     minMessage="Message should have minimum 2 characters and maximum 1000 characters",
     *     maxMessage="Message should have minimum 2 characters and maximum 1000 characters",
     *     groups={"ServiceRequest"}
     * )
     */
    private $text;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime")
     */
    private $createdDate;


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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return ServiceRequest
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set createdDate
     *
     * @return ServiceRequest
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
     * Set typeId
     *
     * @param integer $typeId
     *
     * @return ServiceRequest
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;

        return $this;
    }

    /**
     * Get typeId
     *
     * @return integer
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return ServiceRequest
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set fromUser
     *
     * @param User $fromUser
     *
     * @return ServiceRequest
     */
    public function setFromUser(User $fromUser)
    {
        $this->fromUser = $fromUser;

        return $this;
    }

    /**
     * Get fromUser
     *
     * @return User
     */
    public function getFromUser()
    {
        return $this->fromUser;
    }

    /**
     * Set toUser
     *
     * @param User $toUser
     *
     * @return ServiceRequest
     */
    public function setToUser(User $toUser)
    {
        $this->toUser = $toUser;

        return $this;
    }

    /**
     * Get toUser
     *
     * @return User
     */
    public function getToUser()
    {
        return $this->toUser;
    }
}
