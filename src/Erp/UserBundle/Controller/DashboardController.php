<?php

namespace Erp\UserBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\PropertyBundle\Entity\PropertyRentHistory;
use Erp\PropertyBundle\Entity\Property;
use Erp\StripeBundle\Entity\Invoice;
use Erp\StripeBundle\Entity\Transaction;
use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Entity\Fee;
use Stripe\BankAccount;
use Stripe\Card;
use Symfony\Component\HttpFoundation\Request;
use Erp\UserBundle\Form\Type\UserLateRentPaymentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

//TODO Refactor preparing chart data
class DashboardController extends BaseController {

    /**
     * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_LANDLORD')")
     */
    public function dashboardAction() {
        /** @var User $user */
        $user = $this->getUser();
        return $this->render('ErpUserBundle:Dashboard:index.html.twig', [
                    'user' => $user,
        ]);
    }

    public function showLateRentPaymentsAction() {
        /** @var User $user */
        $user = $this->getUser();
        $feeRepository = $this->getDoctrine()->getManagerForClass(Fee::class)->getRepository(Fee::class);
        $userRepository = $this->getDoctrine()->getManagerForClass(Property::class)->getRepository(Property::class);

        $propertiesWasNotPaid = $userRepository->getDebtors($user);
        $fees = $feeRepository->getFees($user);

        $form = $this->createForm(new UserLateRentPaymentType());

        return $this->render('ErpUserBundle:Dashboard:late_rent_payments.html.twig', [
                    'properties_was_not_paid' => $propertiesWasNotPaid,
                    'fees' => $fees,
                    'form' => $form->createView(),
        ]);
    }

    public function showPropertiesAction() {
        /** @var User $user */
        $user = $this->getUser();

        $now = new \DateTime();
        $sixMonthsAgo = (new \DateTime())->modify('-5 months');
        $labels = $this->getMonthsLabels($sixMonthsAgo, $now);

        $propertyRentHistoryRepo = $this->getDoctrine()->getManagerForClass(PropertyRentHistory::class)->getRepository(PropertyRentHistory::class);
        $history = $propertyRentHistoryRepo->getHistory($user, $sixMonthsAgo, $now);

        $availableProperties = [];
        $rentedProperties = [];
        /** @var PropertyRentHistory $record */
        foreach ($history as $record) {
            $interval = $record['date'];
            switch ($record['status']) {
                case Property::STATUS_DRAFT:
                case Property::STATUS_AVAILABLE:
                    $availableProperties[$interval] = isset($availableProperties[$interval]) ? ++$availableProperties[$interval] : 1;
                    break;
                case Property::STATUS_RENTED:
                    $rentedProperties[$interval] = isset($rentedProperties[$interval]) ? ++$rentedProperties[$interval] : 1;
                    break;
            }
        }

        $intervals = array_keys($labels);
        $labels = array_values($labels);

        $preparedAvailableProperties = [];
        $preparedRentedProperties = [];
        foreach ($intervals as $interval) {
            $preparedAvailableProperties[] = isset($availableProperties[$interval]) ? $availableProperties[$interval] : 0;
            $preparedRentedProperties[] = isset($rentedProperties[$interval]) ? $rentedProperties[$interval] : 0;
        }

        return $this->render('ErpUserBundle:Dashboard:properties_history.html.twig', [
                    'available_properties' => $preparedAvailableProperties,
                    'rented_properties' => $preparedRentedProperties,
                    'labels' => $labels,
                    'intervals' => $intervals,
        ]);
    }

    public function showCashflowsAction() {
        /** @var User $user */
        $user = $this->getUser();

        $now = new \DateTime();
        $sixMonthsAgo = (new \DateTime())->modify('-5 month');
        $stripeAccount = $user->getStripeAccount();
        $stripeCustomer = $user->getStripeCustomer();
        $transactionRepo = $this->getDoctrine()->getManagerForClass(Transaction::class)->getRepository(Transaction::class);

        $cashOut = [];
        if ($stripeAccount || $stripeCustomer) {
            $cashOut = $transactionRepo->getGroupedTransactions(null, $stripeCustomer, $sixMonthsAgo, $now);
        }

        $cashIn = [];
        if ($stripeAccount) {
            $cashIn = $transactionRepo->getGroupedTransactions($stripeAccount, null, $sixMonthsAgo, $now);
        }

        //TODO Refactoring this
        $labels = $this->getMonthsLabels($sixMonthsAgo, $now);
        $intervals = array_keys($labels);
        $labels = array_values($labels);
        $cashIn = $this->getPreparedItems($cashIn, $intervals);
        $cashOut = $this->getPreparedItems($cashOut, $intervals);

        if (!$stripeAccount || !$stripeCustomer) {
            $cashIn = 0;
            $cashOut = 0;
        }
        return $this->render('ErpUserBundle:Dashboard:cashflows.html.twig', [
                    'cash_in' => $cashIn,
                    'cash_out' => $cashOut,
                    'labels' => $labels,
                    'intervals' => $intervals,
        ]);
    }

    public function showInvoicesAction() {
        /** @var User $user */
        $user = $this->getUser();

        $now = new \DateTime();
        $sixMonthsAgo = (new \DateTime())->modify('-5 month');
        $stripeAccount = $user->getStripeAccount();
        $stripeCustomer = $user->getStripeCustomer();

        $items = [];
        if ($stripeAccount || $stripeCustomer) {
            $invoicesRepo = $this->getDoctrine()->getManagerForClass(Invoice::class)->getRepository(Invoice::class);
            $items = $invoicesRepo->getGroupedInvoices($stripeAccount, $stripeCustomer, $sixMonthsAgo, $now);
        }

        $labels = $this->getMonthsLabels($sixMonthsAgo, $now);
        $intervals = array_keys($labels);
        $labels = array_values($labels);
        $invoices = $this->getPreparedItems($items, $intervals);

        return $this->render('ErpUserBundle:Dashboard:invoices.html.twig', [
                    'labels' => $labels,
                    'invoices' => [],
                    'intervals' => $intervals,
        ]);
    }

    public function showTransactionsAction(Request $request) {
        /** @var User $user */
        $user = $this->getUser();
        $stripeAccount = $user->getStripeAccount();
        $stripeCustomer = $user->getStripeCustomer();

        if (!$stripeAccount || !$stripeCustomer) {
            return $this->render('ErpUserBundle:Dashboard:transactions.html.twig', [
                        'pagination' => [],
            ]);
        }

        $repository = $this->getDoctrine()->getManagerForClass(Transaction::class)->getRepository(Transaction::class);
        $query = $repository->getTransactionsQuery($stripeAccount, $stripeCustomer);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($query, $request->query->getInt('page', 1));

        return $this->render('ErpUserBundle:Dashboard:transactions.html.twig', [
                    'pagination' => $pagination,
        ]);
    }

    public function showPaymentDetailsAction() {
        /** @var User $user */
        $user = $this->getUser();
        //TODO Add cache layer (APC or Doctrine)
        $stripeUserManager = $this->get('erp_stripe.stripe.entity.user_manager');
        /** @var BankAccount $bankAccount */
        $bankAccount = $stripeUserManager->getBankAccount($user);
        /** @var Card $creditCard */
        $creditCard = $stripeUserManager->getCreditCard($user);

        return $this->render('ErpPaymentBundle:Stripe/Widgets:payment-details.html.twig', [
                    'creditCard' => $creditCard,
                    'bankAccount' => $bankAccount,
        ]);
    }

    private function getMonthsLabels(\DateTime $dateFrom, \DateTime $dateTo) {
        $dateFrom = \DateTimeImmutable::createFromMutable($dateFrom);
        $dateTo = \DateTimeImmutable::createFromMutable($dateTo);

        $diff = $dateFrom->diff($dateTo);
        $count = ($diff->format('%y') * 12) + $diff->format('%m') + 1;

        $labels = [];
        for ($i = 1; $i <= $count; $i++) {
            $labels[$dateFrom->format('Y-n')] = $dateFrom->format('F');
            $dateFrom = $dateFrom->modify('+1 month');
        }

        return $labels;
    }

    private function getPreparedItems(array $items, array $intervals) {
        //TODO Refactoring amount
        $results = [];
        $existingIntervals = array_column($items, 'interval');

        foreach ($intervals as $interval) {
            if (false !== $key = array_search($interval, $existingIntervals)) {
                $results[] = $items[$key]['gAmount'] / 100;
            } else {
                $results[] = 0;
            }
        }

        return $results;
    }

}
