<?php

namespace Erp\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HomePageContent
 *
 * @ORM\Table(name="homepage_content")
 * @ORM\Entity(repositoryClass="Erp\SiteBundle\Repository\HomePageContentRepository")
 */
class HomePageContent
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="service_body", type="text")
     */
    protected $serviceBody;

    /**
     * @var string
     *
     * @ORM\Column(name="feature_body", type="text")
     */
    protected $featureBody;


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
     * Set serviceBody
     *
     * @param string $serviceBody
     *
     * @return HomePageContent
     */
    public function setServiceBody($serviceBody)
    {
        $this->serviceBody = $serviceBody;

        return $this;
    }

    /**
     * Get serviceBody
     *
     * @return string
     */
    public function getServiceBody()
    {
        return $this->serviceBody;
    }

    /**
     * Set featureBody
     *
     * @param string $featureBody
     *
     * @return HomePageContent
     */
    public function setFeatureBody($featureBody)
    {
        $this->featureBody = $featureBody;

        return $this;
    }

    /**
     * Get featureBody
     *
     * @return string
     */
    public function getFeatureBody()
    {
        return $this->featureBody;
    }
}
