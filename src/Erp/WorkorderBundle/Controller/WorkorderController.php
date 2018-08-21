<?php

namespace Erp\WorkorderBundle\Controller;


use Erp\VendorBundle\Entity\Vendor;
use Erp\WorkorderBundle\Entity\Edit;
use Erp\WorkorderBundle\Entity\Service;
use Erp\WorkorderBundle\Form\EditType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Erp\CoreBundle\Controller\BaseController;
use Erp\WorkorderBundle\Entity\Workorder;

class WorkorderController extends BaseController
{
    /**
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function indexAction(Request $request)
    {
        return $this->render('ErpWorkorderBundle:Workorder:index.html.twig');
    }

    /**
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function showWorkorderAction(Request $request)
    {
        /** @var TokenStorage $tokenStorage */
        $tokenStorage = $this->get('security.token_storage');
        /** @var User $user */
        $user = $tokenStorage->getToken()->getUser();
        $account = $user->getStripeAccount();

        $pagination = [];
        if ($account) {
            $accountId = $account ? $account->getId() : null;

            $em = $this->getDoctrine()->getManager();
            $repository = $this->getDoctrine()->getRepository(Workorder::class);


            $query = $repository->getWorkOrderQuery($accountId);

            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1)
            );
        }

        $vendor_rep = $this->getDoctrine()->getRepository(Vendor::class);
        $vendor_list = $vendor_rep->getVendorList();

        $template = sprintf('ErpWorkorderBundle:Workorder:workorder_list.html.twig');
        $parameters = [
            'user' => $user,
            'status' => ['Created', 'Processing', 'Complete'],
            'contractor' => $vendor_list,
            'pagination' => $pagination,
        ];

        return $this->render($template, $parameters);
    }

    /**
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function createAction(Request $request)
    {
        $user = $this->getUser();
        $manager = $user->getStripeAccount();

        $action = $this->generateUrl('erp_workorder_create');
        $formOptions = ['action' => $action, 'method' => 'POST'];

        $vendor_rep = $this->getDoctrine()->getRepository(Vendor::class);
        $vendor_list = $vendor_rep->getVendorList();

        $workorder = $this->em->getRepository('ErpWorkorderBundle:Edit')->getNextId();
        $workorderId = $workorder->getId()+1;

        $entity = new Edit();
        $form = $this->createForm(new EditType($manager->getId(), $vendor_list), $entity, $formOptions);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $this->em->persist($entity);
                $this->em->flush();

                $serviceData = $request->request->get('serviceData');
                if ($serviceData != '') {
                    $serviceData = json_decode($serviceData, true);

                    foreach ($serviceData as $item) {
                        $serviceEntity = new Service();

                        $serviceEntity->setTaskName($item['task_name']);
                        $serviceEntity->setHours($item['hours']);
                        $serviceEntity->setRate($item['rate']);
                        $serviceEntity->setTaxCode($item['tax_code']);
                        $serviceEntity->setWorkorderId($workorderId);
                        $serviceEntity->setActions(1);

                        $this->em->persist($serviceEntity);
                        $this->em->flush();
                    }
                }


                $this->addFlash('alert_ok', 'Work Order has been added successfully!');

                return $this->redirect($this->generateUrl('erp_workorder_index'));
            }
        }

        return $this->render('ErpWorkorderBundle:CreateForm:eidt.html.twig', [
            'user' => $user,
            'workorderId' => $workorderId,
            'form' => $form->createView()
        ]);

    }

    public function updateAction(Request $request)
    {
        $user = $this->getUser();
        $manager = $user->getStripeAccount();

        $action = $this->generateUrl('erp_workorder_update');
        $formOptions = ['action' => $action, 'method' => 'POST'];

        $entity = new Edit();
        $form = $this->createForm(new EditType($manager->getId()), $entity, $formOptions);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->em->persist($entity);
            $this->em->flush();

            $this->addFlash('alert_ok', 'Work Order has been added successfully!');

            return $this->redirect($this->generateUrl('erp_workorder_index'));
        }

        return $this->render('ErpWorkorderBundle:CreateForm:eidt.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);

    }


}
