<?php

namespace Erp\UserBundle\Controller;

use Erp\PropertyBundle\Entity\Property;
use Erp\CoreBundle\Entity\EmailNotification;
use Erp\CoreBundle\Event\EmailNotificationEvent;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Entity\Message;
use Erp\UserBundle\Form\Type\MessagesFormType;
use Erp\CoreBundle\Controller\BaseController;

/**
 * Class MessageController
 *
 * @package Erp\UserBundle\Controller
 */
class MessageController extends BaseController
{
    /**
     * Page message
     *
     * @param Request $request
     * @param int     $toUserId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function indexAction(Request $request, $toUserId = null)
    {
        /** @var User $user */
        $user = $this->getUser();
        $companions = $this->getCompanions($user);
        if ($toUserId === null && count($companions)) {
            return $this->redirectToRoute('erp_user_messages', ['toUserId' => $companions[0]->getId()]);
        }
        /** @var User $toUser */
        $toUser = $this->em->getRepository('ErpUserBundle:User')->findOneBy(['id' => $toUserId]);

        if (!$toUserId) {
            $renderParams = ['user' => $user];
        } elseif (!$toUser
            || ($user->hasRole(User::ROLE_MANAGER) && !$user->isTenant($toUser))
            || ($user->hasRole(User::ROLE_TENANT) && $user->getTenantProperty()->getUser() != $toUser)
        ) {
            throw $this->createNotFoundException();
        } else {
            $message = new Message();

            $action = $this->generateUrl('erp_user_messages', ['toUserId' => $toUserId]);
            $formOptions = ['action' => $action, 'method' => 'POST'];
            $form = $this->createForm(new MessagesFormType(), $message, $formOptions);

            if ($request->getMethod() === 'POST') {
                $form->handleRequest($request);

                if ($form->isValid()) {
                    $message->setFromUser($user)->setToUser($toUser);
                    $this->em->persist($message);
                    $this->em->flush();
                    $this->checkDispatch($toUser, $user);

                    $msg = 'Message Manager: Message has been successfully sent';
                    $this->get('session')->getFlashBag()->add('alert_ok', $msg);

                    return $this->redirect($request->headers->get('referer'));
                }
            }

            foreach ($companions as $key => $companion) {
                $companions[$key] = [
                    $companion,
                    'totalMessages' => $this->getTotalMessagesByToUser($user, $companion)
                ];
            }

            $messages = $this->getMessages($user, $toUser);
            $this->setUnreadMessages($user, $messages);

            $renderParams = [
                'form' => $form->createView(),
                'user' => $user,
                'companions' => $companions,
                'currentCompanion' => $toUser,
                'messages' => $messages
            ];
        }

        return $this->render('ErpUserBundle:Messages:messages.html.twig', $renderParams);
    }

    /**
     * Return list companions
     *
     * @param User $user
     *
     * @return array
     */
    public function getCompanions(User $user)
    {
        $companions = [];

        // Get companions
        if ($user->hasRole(User::ROLE_MANAGER)) {
            $properties = $user->getProperties();
            /** @var Property $property */
            foreach ($properties as $property) {
                if ($property->getTenantUser()) {
                    $companions[] = $property->getTenantUser();
                }
            }
        } else {
            if ($user->getTenantProperty()) {
                $companions[] = $user->getTenantProperty()->getUser();
            }
        }

        return $companions;
    }

    /**
     * Return count messages for user
     *
     * @param User $fromUser
     * @param User $toUser
     *
     * @return int
     */
    public function getTotalMessagesByToUser(User $fromUser, User $toUser)
    {
        $totalMessages = $this->getDoctrine()
            ->getRepository('ErpUserBundle:Message')
            ->getTotalMessagesByToUser($fromUser, $toUser)
        ;

        return $totalMessages;
    }

    /**
     * Return list messages
     *
     * @param User $user
     * @param User $toUser
     *
     * @return mixed
     */
    public function getMessages(User $user, User $toUser)
    {
        $messages = $this->getDoctrine()
            ->getRepository('ErpUserBundle:Message')
            ->getMessages($user, $toUser)
        ;

        return $messages;
    }

    /**
     * Set unread messages
     *
     * @param $messages
     *
     * @return bool
     */
    public function setUnreadMessages(User $user, $messages)
    {
        if ($messages) {
            foreach ($messages as $message) {
                if ($message->getToUser() === $user) {
                    $message->setIsRead(true);
                    $this->em->persist($message);
                    $this->em->flush();
                }
            }
        }

        return true;
    }

    /**
     * @param User $toUser
     * @param User $user
     *
     * @return $this
     */
    protected function checkDispatch(User $toUser, User $user)
    {
        if ($toUser->hasRole(User::ROLE_MANAGER)) {
            $event = new EmailNotificationEvent(
                $toUser,
                EmailNotification::SETTING_PROFILE_MESSAGES,
                [
                    '#url#' => $this->generateUrl('erp_user_messages', ['toUserId' => $user->getId()], true)
                ]
            );

            /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch(EmailNotification::EVENT_SEND_EMAIL_NOTIFICATION, $event);
        }

        return $this;
    }
}
