<?php

namespace Erp\NotificationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Erp\NotificationBundle\Form\Type\UserNotificationType;
use Erp\NotificationBundle\Entity\UserNotification;
use Erp\PropertyBundle\Entity\Property;
use Doctrine\ORM\QueryBuilder;
use Erp\UserBundle\Entity\User;
use Erp\NotificationBundle\Entity\Template;

class UserNotificationController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $entity = new UserNotification();
        return $this->update($entity, $request);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        $repo = $this->getDoctrine()->getManagerForClass(UserNotification::class)->getRepository(UserNotification::class);
        $alerts = $repo->getAlertsByUser($user);

        return $this->render('ErpNotificationBundle:UserNotification:list.html.twig', [
            'alerts' => $alerts,
        ]);
    }

    public function updateAction($id, Request $request)
    {
        $alert = $this->getAlert($id);
        return $this->update($alert, $request);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function choosePropertiesAction(Request $request, $id = null)
    {
        $path = '';
        $userNotification = null;
        if ($id) {
            $userNotification = $this->getAlert($id);
            $path = $this->generateUrl('erp_notification_user_notification_choose_properties_update', ['id' => $id]);
        } else {
            $userNotification = new UserNotification();
            $path = $this->generateUrl('erp_notification_user_notification_choose_properties');
        }
        $form = $this->getForm($userNotification, ['action' => $path]);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->redirectToRoute('erp_notification_user_notification_list');
        }

        /** @var User $user */
        $user = $this->getUser();

        $idx = $request->get('idx', []);
        $allElements = $request->get('all_elements', false);

        $em = $this->getDoctrine()->getManagerForClass(Property::class);
        $propertyRepository = $em->getRepository(Property::class);
        /** @var QueryBuilder $qb */
        $qb = $propertyRepository->getQueryBuilderByUser($user);

        if (count($idx) > 0) {
            $propertyRepository->addIdentifiersToQueryBuilder($qb, $idx);
        } elseif (!$allElements) {
            $qb = null;
        }

        $em = $this->getDoctrine()->getManagerForClass(UserNotification::class);
        try {
            $i = 0;
            $userNotification->eraseProperties();
            if ($qb) {
                foreach ($qb->getQuery()->iterate() as $object) {
                    /** @var Property $property */
                    $property = $object[0];
                    $userNotification->addProperty($property);
                    $em->persist($userNotification);

                    if ((++$i % 20) == 0) {
                        $em->flush();
                        $em->clear();
                    }
                }
            }

            $em->flush();
            $em->clear();

            $this->addFlash(
                'alert_ok',
                'Success'
            );
        } catch (\PDOException $e) {
            throw $e;
        }

        return $this->redirectToRoute('erp_notification_user_notification_list');
    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAlertAction($id)
    {
        /** @var User $user */
        $user = $this->getUser();

        $repo = $this->getDoctrine()->getManagerForClass(UserNotification::class)->getRepository(UserNotification::class);
        /** @var UserNotification $alert */
        $alert = $repo->getAlertByUserAndId($id, $user);

        return $this->render('ErpNotificationBundle:UserNotification:view_alert.html.twig', [
            'alert' => $alert,
        ]);
    }

    private function update(UserNotification $entity, Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $repo = $this->getDoctrine()->getManagerForClass(Property::class)->getRepository(Property::class);
        $properties = $repo->getPropertiesWithActiveTenant($user);
        if (!$properties) {
            $this->addFlash(
                'alert_error',
                'Sorry, you don\'t have properties with active tenants to create/edit alerts'
            );
            return $this->redirectToRoute('erp_notification_user_notification_list');
        }

        $path = '';
        if ($entity->getId()) {
            $path = $this->generateUrl('erp_notification_user_notification_update', ['id' => $entity->getId()]);
        } else {
            $path = $this->generateUrl('erp_notification_user_notification_create');
        }
        $form = $this->getForm($entity, ['action' => $path]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            // TODO: fix this hack to not lose data
            $form = $this->getForm($entity, ['action' => $path]);
            $form->handleRequest($request);

            if ($entity->getId()) {
                $action = $this->generateUrl('erp_notification_user_notification_choose_properties_update', ['id' => $entity->getId()]);
            } else {
                $action = $this->generateUrl('erp_notification_user_notification_choose_properties');
            }
            return $this->render('ErpNotificationBundle:UserNotification:choose_properties.html.twig', [
                'properties' => $properties,
                'form' => $form->createView(),
                'action' => $action,
                'entity' => $entity,
            ]);
        }

        return $this->render('ErpNotificationBundle:UserNotification:create.html.twig', [
            'form' => $form->createView(),
            'entity' => $entity
        ]);
    }

    private function getUserTemplates()
    {
        $repository = $this->getDoctrine()->getManagerForClass(Template::class)->getRepository(Template::class);
        return $repository->getTemplatesByUser($this->getUser());
    }

    private function getForm(UserNotification $userNotification = null, array $extra = [])
    {
        $options = [
            'templates' => $this->getUserTemplates(),
        ];
        return $this->createForm(new UserNotificationType(), $userNotification, array_merge($options, $extra));
    }

    private function getAlert($id)
    {
        $repo = $this->getDoctrine()->getManagerForClass(UserNotification::class)->getRepository(UserNotification::class);
        $alert = $repo->getAlertByUserAndId($id, $this->getUser());
        if (!$alert) {
            throw new NotFoundHttpException('Alert with id '.$id.' not found');
        }
        return $alert;
    }
}
