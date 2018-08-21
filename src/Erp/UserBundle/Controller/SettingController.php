<?php

namespace Erp\UserBundle\Controller;

use Erp\UserBundle\Form\Type\SettingFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Erp\CoreBundle\Controller\BaseController;

/**
 * Class SettingController
 *
 * @package Erp\UserBundle\Controller
 */
class SettingController extends BaseController
{
    /**
     * Page settings
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function indexAction(Request $request)
    {
        $user = $this->getUser();
        if (!$user->getSecondEmail()) {
            $user->setSecondEmail($user->getEmail());
        }

        $settings = $this->get('erp.users.user.service')->getSettings();
        $action = $this->generateUrl('erp_user_settings');
        $formOptions = ['action' => $action, 'method' => 'POST'];
        $form = $this->createForm(new SettingFormType($settings), $user, $formOptions);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid() && !$user->isReadOnlyUser()) {
                if ($user->getSecondEmail() === $user->getEmail()) {
                    $user->setSecondEmail(null);
                }

                $this->em->persist($user);
                $this->em->flush();

                return $this->redirectToRoute('erp_user_settings');
            }
        }

        return $this->render(
            'ErpUserBundle:Settings:settings.html.twig',
            [
                'user' => $user,
                'form' => $form->createView()
            ]
        );
    }
}
