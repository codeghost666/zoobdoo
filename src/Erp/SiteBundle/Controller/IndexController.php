<?php

namespace Erp\SiteBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\SiteBundle\Entity\ContactPageRequest;
use Erp\SiteBundle\Entity\StaticPage;
use Erp\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Erp\SiteBundle\Form\Type\ManagerInviteFormType;
use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Erp\SiteBundle\Form\Type\ContactPageRequestFormType;

class IndexController extends BaseController
{
    /**
     * Homepage
     *
     * @return Response
     */
    public function indexAction()
    {
        $properties = $this->em->getRepository('ErpPropertyBundle:Property')
            ->findAvailable($this->getUser());
        $slider = $this->em->getRepository('ErpSiteBundle:HomePageSlider')
            ->findAll();
        $content = $this->em->getRepository('ErpSiteBundle:HomePageContent')
            ->findLast();

        return $this->render(
            'ErpSiteBundle:Home:index.html.twig',
            [
                'hideHeaderBanner' => true,
                'properties' => $properties,
                'slider' => $slider,
                'content' => $content
            ]
        );
    }

    /**
     * Contact form
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function contactPageAction(Request $request)
    {
        $contactPageRequest = new ContactPageRequest();
        $form = $this->createContactPageRequestForm($contactPageRequest);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                // send email

                $formData = $request->request->get($form->getName());

                $fees = $this->get('erp.core.fee.service')->getFees();
                $defaultEmail = $fees ? $fees->getDefaultEmail() : '';

                $emailParams = [
                    'sendTo' => $defaultEmail,
                    'url' => $this->generateUrl(
                        'admin_erpsitebundle_contactpagerequests_list',
                        [],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ),
                    'formData' => $formData
                ];

                $emailType = EmailNotificationFactory::TYPE_CONTACT_FORM_TO_ADMIN;
                $this->container->get('erp.core.email_notification.service')
                    ->sendEmail($emailType, $emailParams);

                $this->em->persist($contactPageRequest);
                $this->em->flush();
                $this->get('session')->getFlashBag()->add('alert_ok', 'Your message was sent successfully');

                return $this->redirectToRoute('erp_site_contact_page');
            }
        }

        $staticPageRepository = $this->em->getRepository('ErpSiteBundle:StaticPage');
        $staticPage = $staticPageRepository->findOneBy(['code' => 'contact']);

        if (!$staticPage instanceof StaticPage) {
            throw new NotFoundHttpException('Page not found');
        }

        $submenuStaticPages = $staticPageRepository->findBy(['inSubmenu' => true]);

        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->get('router')->generate('erp_site_homepage'));
        $breadcrumbs->addItem('Contact');

        return $this->render(
            'ErpSiteBundle:StaticPage:contact.html.twig',
            [
                'form' => $form->createView(),
                'page' => $staticPage,
                'submenuPages' => $submenuStaticPages
            ]
        );
    }

    /**
     * Popup - send invite to manager
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function sendInviteToManagerAction(Request $request)
    {
        $type = new ManagerInviteFormType();
        $action = $this->generateUrl('erp_site_send_invite_to_manager');

        $formOptions = ['action' => $action, 'method' => 'POST'];
        $form = $this->createForm($type, null, $formOptions);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $formData = $request->request->all()[$form->getName()];

                $emailParams = [
                    'sendTo' => $formData['managerEmail'],
                    'managerInvite' => $formData,
                ];

                /** @var User $toUser */
                $manager =
                    $this->em->getRepository('ErpUserBundle:User')->findOneBy(['email' => $formData['managerEmail']]);

                if ($manager) {
                    if ($manager->getStatus() == User::STATUS_DISABLED
                        or $manager->getStatus() == User::STATUS_REJECTED
                    ) {
                        // show error on contact page

                        $error = 'Invite to this Manager cannot be sent. Contact Administrator for details.';

                        $this->get('session')->getFlashBag()->add('alert_error', $error);

                        return $this->redirectToRoute('erp_site_contact_page');
                    } else {
                        $emailType = EmailNotificationFactory::TYPE_MANAGER_ACTIVE_INVITE;

                        $emailParams['url'] = $this->generateUrl(
                            'fos_user_security_login',
                            [],
                            UrlGeneratorInterface::ABSOLUTE_URL
                        );
                    }
                } else {
                    $emailType = EmailNotificationFactory::TYPE_MANAGER_INVITE;

                    $emailParams['url'] = $this->generateUrl(
                        'fos_user_registration_register',
                        [],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    );
                }

                $this->container->get('erp.core.email_notification.service')
                    ->sendEmail($emailType, $emailParams);

                return $this->redirectToRoute('erp_site_homepage');
            }
        }

        return $this->render(
            'ErpSiteBundle:Form:send-invite-to-manager-form.html.twig',
            [
                'modalTitle' => 'Invite Your Property Manager to Join!',
                'form' => $form->createView()
            ]
        );
    }

    /**
     * Return static page
     *
     * @param string $slug
     *
     * @return Response
     */
    public function staticPageAction($slug)
    {
        $staticPageRepository = $this->em->getRepository('ErpSiteBundle:StaticPage');
        $staticPage = $staticPageRepository->findOneBy(['slug' => $slug]);

        if (!$staticPage instanceof StaticPage) {
            throw new NotFoundHttpException('Page not found');
        }

        $submenuStaticPages = $staticPageRepository->findBy(['inSubmenu' => true]);

        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->get('router')->generate('erp_site_homepage'));
        $breadcrumbs->addItem(
            $staticPage->getHeaderTitle(),
            $this->get('router')->generate('erp_site_static_page', ['slug' => $staticPage->getSlug()])
        );

        return $this->render(
            'ErpSiteBundle:StaticPage:' . $staticPage->getTemplate() . '.html.twig',
            [
                'page' => $staticPage,
                'submenuPages' => $submenuStaticPages,
            ]
        );
    }

    /**
     * Create contact page form
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createContactPageRequestForm($contactPageRequest)
    {
        $actionForm = $this->generateUrl('erp_site_contact_page');
        $formOptions = ['action' => $actionForm, 'method' => 'POST'];
        $form = $this->createForm(new ContactPageRequestFormType(), $contactPageRequest, $formOptions);

        return $form;
    }
}
