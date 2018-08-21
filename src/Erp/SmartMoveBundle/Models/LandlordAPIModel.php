<?php

namespace Erp\SmartMoveBundle\Models;

use Erp\UserBundle\Entity\User;
use Erp\SmartMoveBundle\Entity\SmartMoveRenter;

/**
 * Class LandlordAPIModel
 *
 * @package Erp\SmartMoveBundle\Models
 */
class LandlordAPIModel implements SmartMoveModelInterface
{
    /**
     * @var User
     */
    protected $landlord = null;

    /**
     * @var SmartMoveRenter
     */
    protected $smRenter = null;

    /**
     * @var string
     */
    protected $email = null;

    /**
     * Set tenant
     *
     * @param User $landlord
     *
     * @return $this
     */
    public function setLandlord(User $landlord)
    {
        $this->landlord = $landlord;

        return $this;
    }

    /**
     * Get tenant
     *
     * @return User|null
     */
    public function getLandlord()
    {
        return $this->landlord;
    }

    /**
     * Set smRenter
     *
     * @param SmartMoveRenter $smRenter
     *
     * @return $this
     */
    public function setSMRenter(SmartMoveRenter $smRenter)
    {
        $this->smRenter = $smRenter;

        return $this;
    }

    /**
     * Get smRenter
     *
     * @return SmartMoveRenter|null
     */
    public function getSMRenter()
    {
        return $this->smRenter;
    }

    /**
     * Set tenant
     *
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get tenant
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
}
