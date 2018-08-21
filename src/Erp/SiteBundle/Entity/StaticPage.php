<?php

namespace Erp\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * StaticPage
 *
 * @ORM\Table(name="static_pages")
 * @ORM\Entity(repositoryClass="Erp\SiteBundle\Repository\StaticPageRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class StaticPage
{
    const PAGE_CODE_TERMS_OF_USE = 'terms-of-service';

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
     * @ORM\Column(name="code", type="string", length=255)
     */
    protected $code;

    /**
     * @var string
     *
     * @ORM\Column(name="template", type="string", length=255)
     */
    protected $template;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     */
    protected $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_title", type="string", length=255, nullable=true)
     */
    protected $metaTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_description", type="text", nullable=true)
     */
    protected $metaDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="header_title", type="string", length=255)
     *
     * @Assert\NotBlank(message="Please fill out the field", groups={"StaticPage"})
     *
     * @Assert\Length(
     *     min=2,
     *     max=255,
     *     minMessage="Header title should have minimum 2 characters and maximum 255 characters",
     *     maxMessage="Header title should have minimum 2 characters and maximum 255 characters",
     *     groups={"StaticPage"}
     * )
     */
    protected $headerTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     *
     * @Assert\NotBlank(message="Please fill out the field", groups={"StaticPage"})
     */
    protected $content;

    /**
     * @var boolean
     *
     * @ORM\Column(name="in_submenu", type="boolean")
     */
    protected $inSubmenu = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="with_submenu", type="boolean")
     */
    protected $withSubmenu = true;

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
     * Set code
     *
     * @param string $code
     *
     * @return StaticPage
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set template
     *
     * @param string $template
     *
     * @return StaticPage
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return StaticPage
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set metaTitle
     *
     * @param string $metaTitle
     *
     * @return StaticPage
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    /**
     * Get metaTitle
     *
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * Set metaDescription
     *
     * @param string $metaDescription
     *
     * @return StaticPage
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * Get metaDescription
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * Set headerTitle
     *
     * @param string $headerTitle
     *
     * @return StaticPage
     */
    public function setHeaderTitle($headerTitle)
    {
        $this->headerTitle = $headerTitle;

        return $this;
    }

    /**
     * Get headerTitle
     *
     * @return string
     */
    public function getHeaderTitle()
    {
        return $this->headerTitle;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return StaticPage
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return StaticPage
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set inSubmenu
     *
     * @param boolean $inSubmenu
     *
     * @return StaticPage
     */
    public function setInSubmenu($inSubmenu)
    {
        $this->inSubmenu = $inSubmenu;

        return $this;
    }

    /**
     * Get inSubmenu
     *
     * @return boolean
     */
    public function getInSubmenu()
    {
        return $this->inSubmenu;
    }

    /**
     * Set withSubmenu
     *
     * @param boolean $withSubmenu
     *
     * @return StaticPage
     */
    public function setWithSubmenu($withSubmenu)
    {
        $this->withSubmenu = $withSubmenu;

        return $this;
    }

    /**
     * Get withSubmenu
     *
     * @return boolean
     */
    public function getWithSubmenu()
    {
        return $this->withSubmenu;
    }

    /**
     * Set createdDate
     *
     * @ORM\PrePersist
     *
     * @return $this
     */
    public function setCreatedDate()
    {
        $this->createdDate = new \DateTime();

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
     * Set updatedDate
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     *
     * @return $this
     */
    public function setUpdatedDate()
    {
        $this->updatedDate = new \DateTime();

        return $this;
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
}
