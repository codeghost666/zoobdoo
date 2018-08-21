<?php

namespace Erp\UserBundle\Twig;

use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Entity\UserDocument;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class UserExtension
 *
 * @package Erp\UserBundle\Twig
 */
class UserExtension extends \Twig_Extension {

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var array
     */
    protected $countUnreadMessages = [];

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
    }

    /**
     * @return string
     */
    public function getName() {
        return 'user_extension';
    }

    /**
     * @return array
     */
    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('count_unread_messages', [$this, 'getCountUnreadMessages']),
            new \Twig_SimpleFunction('get_pdf_link_signed', [$this, 'getPdfLinkOfSignedDocument']),
            new \Twig_SimpleFunction('isavailable_pdf_signed', [$this, 'isSignedPDFAvailable']),
        ];
    }

    public function getFilters() {
        return [
            new \Twig_SimpleFilter('is_sender_signing', [$this, 'checkIsSenderSigning']),
        ];
    }

    /**
     * Return count unread messages
     *
     * @param User $user
     * @param User $fromUser
     *
     * @return int
     */
    public function getCountUnreadMessages(User $user, User $fromUser = null) {
        if ($fromUser) {
            if (!isset($this->countUnreadMessages[$fromUser->getId()])) {
                $count = $this->em->getRepository('ErpUserBundle:Message')->getCountUnreadMessages($user, $fromUser);
                $this->countUnreadMessages[$fromUser->getId()] = $count;
            } else {
                $count = $this->countUnreadMessages[$fromUser->getId()];
            }
        } else {
            $count = $this->em->getRepository('ErpUserBundle:Message')->getCountUnreadMessages($user);
        }

        return $count;
    }

    /**
     * 
     * @param \Erp\UserBundle\Entity\UserDocument $userDocument
     * @return string
     */
    public function getPdfLinkOfSignedDocument(\Erp\UserBundle\Entity\UserDocument $userDocument) {
        $signatureId = $userDocument->getEnvelopIdToUser();
        return $this->container->get('erp.signature.hellosign.service')->getPdfLink($signatureId)->file_url;
    }

    /**
     * 
     * @param \Erp\UserBundle\Entity\UserDocument $userDocument
     * @return boolean
     */
    public function isSignedPDFAvailable(\Erp\UserBundle\Entity\UserDocument $userDocument) {
        $signatureId = $userDocument->getEnvelopIdToUser();

        return !(is_null($this->container->get('erp.signature.hellosign.service')->getPdfLink($signatureId)->file_url));
    }

    /**
     * 
     * @param UserDocument $userDocument
     * @param User $user
     * @return boolean
     */
    public function checkIsSenderSigning(UserDocument $userDocument, User $user) {
        return $this->container->get('erp.signature.hellosign.service')->isFromUserSigning($user, $userDocument);
    }

}
