<?php

namespace Erp\UserBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as BaseController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SecurityController
 *
 * @package Erp\UserBundle\Controller
 */
class SecurityController extends BaseController
{
    /**
     * Login page
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function loginAction(Request $request)
    {
        if ($this->getUser()) {
            $response = $this->redirectToRoute('erp_user_profile_dashboard');
        } else {
            $response = parent::loginAction($request);
        }

        return $response;
    }
}
