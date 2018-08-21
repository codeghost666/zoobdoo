<?php

namespace Erp\UserBundle\Controller;

use Erp\StripeBundle\Entity\Transaction;
use Erp\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Erp\CoreBundle\Controller\BaseController;
use Erp\StripeBundle\Form\Type\TransactionFilterType;
use Erp\UserBundle\Form\Type\ChargeFilterType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Erp\StripeBundle\Form\Type\AbstractFilterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class AccountingController extends BaseController {

    /**
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function indexAction(Request $request) {
        return $this->render('ErpUserBundle:Accounting:index.html.twig');
    }

    /**
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function listInvoicesAction(Request $request) {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();

        $form = $this->createForm(new ChargeFilterType());
        $form->handleRequest($request);

        $data = $form->getData();

        $dateFrom = $data['dateFrom'];
        $dateTo = $data['dateTo'];

        $repo = $this->em->getRepository(\Erp\UserBundle\Entity\Charge::class);

        $_format = $request->get('_format', 'html');
        if ($_format != 'html' && $_format != 'pdf') {
            $_format = 'html';
        }
        $template = sprintf('ErpUserBundle:Accounting:accounting_invoices_list.%s.twig', $_format);

        if ($_format == 'html') {
            $query = $repo->getQueryChargesByManager($user, $dateFrom, $dateTo);
            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate($query, $request->query->getInt('page', 1));

            $urlParameters = array_merge(
                    array('_format' => 'pdf'), array('filter' => $this->getFilterParameters($request))
            );

            $parameters = array(
                'user' => $user,
                'form' => $form->createView(),
                'pagination' => $pagination,
                'pdf_link' => $this->generateUrl('erp_user_accounting_list_invoices', $urlParameters),
            );

            return $this->render($template, $parameters);
        } elseif ($_format == 'pdf') {
            $parameters = array(
                'pagination' => $repo->findChargesByManager($user, $dateFrom, $dateTo),
            );

            $fileName = sprintf('accounting_invoice_list_%s.pdf', (new \DateTime())->format('d_m_Y__H_i_s'));
            $html = $this->renderView($template, $parameters);
            $pdf = $this->get('knp_snappy.pdf')->getOutputFromHtml($html);

            return new Response($pdf, Response::HTTP_OK, array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ));
        }
    }

    /**
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function showAccountingInvoiceAction(Request $request, $invoiceId, $_format = 'html') {
        try {
            /** @var $user \Erp\UserBundle\Entity\User */
            $user = $this->getUser();

            $charge = $this->em->getRepository(\Erp\UserBundle\Entity\Charge::class)->find($invoiceId);

            $template = 'ErpUserBundle:Landlords:charge_email_template.html.twig';
            $parameters = array(
                'sender' => $user,
                'charge' => $charge,
            );

            if ($_format == 'html') {
                $parameters['pdf_link'] = $this->generateUrl('erp_user_accounting_show_invoice', array(
                    '_format' => 'pdf',
                    'invoiceId' => $invoiceId,
                ));

                return \Symfony\Bundle\FrameworkBundle\Controller\Controller::render($template, $parameters);
            } elseif ($_format == 'pdf') {
                $fileName = sprintf('accounting_invoice_%s_%s.pdf', $invoiceId, (new \DateTime())->format('d_m_Y__H_i_s'));
                $html = $this->renderView($template, $parameters);
                $pdf = $this->get('knp_snappy.pdf')->getOutputFromHtml($html);

                return new Response($pdf, Response::HTTP_OK, array(
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                ));
            }
        } catch (\Exception $ex) {
            return new Response($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function listAccountingLedgerAction(Request $request, $_format = 'html') {
        /** @var TokenStorage $tokenStorage */
        $tokenStorage = $this->get('security.token_storage');
        /** @var User $user */
        $user = $tokenStorage->getToken()->getUser();

        $requestStack = $this->get('request_stack');
        $masterRequest = $requestStack->getMasterRequest();

        $form = $this->createForm(new TransactionFilterType($tokenStorage));
        $form->handleRequest($masterRequest);

        $data = $form->getData();
        $stripeAccount = $user->getStripeAccount();

        $dateFrom = $data['dateFrom'];
        $dateTo = $data['dateTo'];
        $keyword = $data['keyword'];

        $pagination = [];
        if ($stripeAccount) {
            $stripeAccountId = $stripeAccount ? $stripeAccount->getId() : null;
            $repository = $this->em->getRepository(Transaction::class);
            $query = $repository->getTransactionsSearchQuery($stripeAccountId, null, $dateFrom, $dateTo, null, $keyword);

            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                    $query, $request->query->getInt('page', 1)
            );
        }

        $template = 'ErpUserBundle:Accounting:accounting_ledger_list.html.twig';
        $parameters = [
            'user' => $user,
            'form' => $form->createView(),
            'pagination' => $pagination
        ];

        if ($_format == 'html') {
            $urlParameters = array_merge(
                    ['_format' => 'pdf'], ['filter' => $this->getFilterParameters($masterRequest)]
            );
            $parameters['pdf_link'] = $this->generateUrl('erp_user_accounting_show_accounting_ledger', $urlParameters);

            return $this->render($template, $parameters);
        } elseif ($_format == 'pdf') {
            $fileName = sprintf('accounting_ledger_%s.pdf', (new \DateTime())->format('d_m_Y'));
            $html = $this->renderView($template, $parameters);
            $pdf = $this->get('knp_snappy.pdf')->getOutputFromHtml($html);

            return new Response(
                    $pdf, Response::HTTP_OK, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                    ]
            );
        }
    }

    /**
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function showAccountingLedgerAction(Request $request, $_format = 'html') {
        /** @var TokenStorage $tokenStorage */
        $tokenStorage = $this->get('security.token_storage');
        /** @var User $user */
        $user = $tokenStorage->getToken()->getUser();

        $requestStack = $this->get('request_stack');
        $masterRequest = $requestStack->getMasterRequest();

        $form = $this->createForm(new TransactionFilterType($tokenStorage));
        $form->handleRequest($masterRequest);

        $data = $form->getData();
        $stripeAccount = $user->getStripeAccount();

        $dateFrom = $data['dateFrom'];
        $dateTo = $data['dateTo'];
        $keyword = $data['keyword'];

        $pagination = [];
        if ($stripeAccount) {
            $stripeAccountId = $stripeAccount ? $stripeAccount->getId() : null;
            $repository = $this->em->getRepository(Transaction::class);
            $query = $repository->getTransactionsSearchQuery($stripeAccountId, null, $dateFrom, $dateTo, null, $keyword);

            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                    $query, $request->query->getInt('page', 1)
            );
        }

        $template = sprintf('ErpUserBundle:Accounting:accounting_ledger.%s.twig', $_format);
        $parameters = [
            'user' => $user,
            'form' => $form->createView(),
            'pagination' => $pagination,
        ];

        if ($_format == 'html') {
            $urlParameters = array_merge(
                    ['_format' => 'pdf'], ['filter' => $this->getFilterParameters($masterRequest)]
            );
            $parameters['pdf_link'] = $this->generateUrl('erp_user_accounting_show_accounting_ledger', $urlParameters);

            return $this->render($template, $parameters);
        } elseif ($_format == 'pdf') {
            $fileName = sprintf('accounting_ledger_%s.pdf', (new \DateTime())->format('d_m_Y'));
            $html = $this->renderView($template, $parameters);
            $pdf = $this->get('knp_snappy.pdf')->getOutputFromHtml($html);

            return new Response(
                    $pdf, Response::HTTP_OK, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                    ]
            );
        }
    }

    /**
     * 
     * @param Request $request
     * @return type
     */
    private function getFilterParameters(Request $request) {
        return $request->query->get(AbstractFilterType::NAME, []);
    }

}
