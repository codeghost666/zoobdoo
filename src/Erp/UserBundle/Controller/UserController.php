<?php

namespace Erp\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Form\Type\UserLateRentPaymentType;

class UserController extends Controller
{
    public function allowRentPaymentAction(User $user, Request $request)
    {
        $currentUser = $this->getUser();
        if ($currentUser->hasTenant($user)) {
            return $this->createNotFoundException();
        }

        $form = $this->createForm(new UserLateRentPaymentType(), $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManagerForClass(User::class);
            $em->persist($user);
            $em->flush();

            return new JsonResponse([
                'success' => true,
            ]);
        }

        return new JsonResponse([
            'success' => false,
            'error' => 'An occurred error.',
        ]);
    }
}