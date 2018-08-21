<?php

namespace Erp\SignatureBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\CoreBundle\Entity\Document;
use Erp\PropertyBundle\Form\Type\ESignFormType;
use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Entity\UserDocument;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class SignatureController
 *
 * @package Erp\SignatureBundle\Controller
 */
class SignatureController extends BaseController {

    /**
     * Send document at esign
     *
     * @param Request $request
     * @param         $documentId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function eSignAction(Request $request, $documentId) {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        /** @var Document $document */
        $document = $this->em->getRepository('ErpCoreBundle:Document')->find($documentId);
        if (!$document) {
            throw $this->createNotFoundException('Document not found');
        }

        $form = $this->createESignForm($document);

        $renderOptions = [
            'eSignFee' => $this->get('erp.core.fee.service')->getESignFee(),
            'modalTitle' => 'Enter your email address',
            'isTenant' => $user->hasRole(User::ROLE_TENANT),
        ];

        if ($user = $this->getUser()) {
            $renderOptions = array_merge($renderOptions, ['user' => $user]);
        }

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                // Payment
                $amount = $this->get('erp.core.fee.service')->getESignFee();

                $payer = ($user->hasRole(User::ROLE_TENANT)) ? $user->getTenantProperty()->getUser() : $user;

                $customer = $payer->getPaySimpleCustomers()->first();
                $accountId = $customer->getPrimaryType() === PaySimpleManagerInterface::CREDIT_CARD ? $customer->getCcId() : $customer->getBaId();
                $paymentModel = new RecurringPaymentModel();
                $paymentModel->setAmount($amount)
                        ->setCustomer($customer)
                        ->setStartDate(new \DateTime())
                        ->setAccountId($accountId);

                $paymentResponse = $this->get('erp.users.user.service')->makeOnePayment($paymentModel);

                if (!$paymentResponse['status']) {
                    $this->get('erp.payment.paysimple_service')->sendPaymentEmail($customer);
                    if ($user->hasRole(User::ROLE_TENANT)) {
                        $msg = $this->get('erp.users.user.service')->getPaySimpleErrorByCode('charge_esign_tenant_error');
                    } else {
                        $msg = $this->get('erp.users.user.service')->getPaySimpleErrorByCode('error');
                    }

                    $renderOptions = array_merge(
                            $renderOptions, [
                        'modalTitle' => 'Error',
                        'msg' => $msg,
                            ]
                    );
                } else {
                    $email = $form->get('email')->getData();
                    $response = $this->get('erp.signature.docusign.service')->createEnvelopeFromDocument($document, $email);

                    $this->get('erp.logger')->add('docusign', json_encode($response));
                    if (isset($response->status) && $response->status === 'sent') {
                        $msg = $this->get('erp.users.user.service')->getPaySimpleErrorByCode('charge_esign_tenant_ok')
                                . '<br/>Document to be signed is sent to your email, please check ' . $email . '.';

                        $renderOptions = array_merge(
                                $renderOptions, [
                            'modalTitle' => 'Success',
                            'msg' => $msg,
                                ]
                        );
                    } else {
                        $msg = 'Please try again later or contact your administrator';
                        $renderOptions = array_merge(
                                $renderOptions, [
                            'modalTitle' => 'Error',
                            'msg' => isset($response->message) ? $response->message : $msg
                                ]
                        );
                    }
                }
            }
        }

        $renderOptions = array_merge($renderOptions, ['form' => $form->createView()]);

        return $this->render('ErpPropertyBundle:Form:esign-form.html.twig', $renderOptions);
    }

    /**
     * 
     * @param integer $userDocumentId
     * @return Response|RedirectResponse
     * @throws type
     */
    public function editEnvelopAction($userDocumentId) {
        $repository = $this->em->getRepository(UserDocument::class);
        /** @var UserDocument $document */
        $userDocument = $repository->find($userDocumentId);

        if (!$userDocument || !$userDocument->getDocument()) {
            throw $this->createNotFoundException('Document not found');
        }

        if ($userDocument->isSigned() || $userDocument->isSent()) {
            throw $this->createNotFoundException('Document already signed or signed');
        }

        /** @var User $sender */
        $sender = $this->getUser();

        $recipient = $userDocument->getToUser();

        try {
            $docusignService = $this->get('erp.signature.docusign.service');

            if (!$userDocument->getEnvelopId()) {
                $response = $docusignService->createEnvelopeFromDocumentNew($userDocument->getDocument(), $sender, $recipient);

                $userDocument->setEnvelopId($response->envelopeId);

                $this->em->persist($userDocument);
                $this->em->flush();
            }

            $url = $docusignService->createCorrectLink($userDocument->getEnvelopId(), $recipient);

            return new RedirectResponse($url);
        } catch (\Exception $e) {
            return new Response($e->getMessage());
        }
    }

    /**
     * This function is called by Ajax in order to retrieve the features of the
     * document to sign, and returns a json response with the variable useful
     * for showing the modal with embedded HelloSign API
     * 
     * @param integer $userDocumentId
     * @return Response|RedirectResponse
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function editEnvelopHelloSignAction($userDocumentId) {
        /** @var UserDocument $document */
        $userDocument = $this->em->getRepository(UserDocument::class)->find($userDocumentId);

        list($result, $message) = $this->checkUserDocument($userDocument);
        if ($result) {
            try {
                // check if the current user, who is signing, is a manager but not as applicant
                $myEnvelopId = $this->get('erp.signature.hellosign.service')->getEnvelopIdSigningUser($this->getUser(), $userDocument);
                $signUrl = $this->get('hellosign.client')->getEmbeddedSignUrl($myEnvelopId)->getSignUrl();

                $data = array(
                    'SIGN_URL' => $signUrl,
                    'CLIENT_ID' => $this->getParameter('hellosign_app_clientid'),
                    'TEST_ENV' => $this->getParameter('hellosign_app_testenv')
                );
                
                return new JsonResponse($data, Response::HTTP_OK);
            } catch (\Exception $ex) {
                return new JsonResponse(array('error' => $ex->getMessage()), $ex->getCode());
            }
        } else {
            return new JsonResponse(array('error' => $message), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * This function is called after successful signing of the document, run by
     * an ajax request within ErpUserBundle\Resources\public\js\documentation.controller.js::helloSignGetDocSigned.
     * If HelloSign Javascript client picks up the signing of a document, it runs an Ajax request
     * to the current function.
     * This function keeps the status as is if the signing user is a manager/applicant,
     * otherwise sets the status as PENDING. In this latter situation (landlord/tenant is signing),
     * an email towards the manager is sent to alert of signature.
     * Then, numOfSignature of the UserDocument is increased by one, and compared with
     * UserDocument->getMaxNumOfSignatures(): if they are equal, the status is set
     * to COMPLETED and an email for downloading of signed PDF is sent to both users
     * 
     * @param Request $request
     * @param string $userDocumentId
     * @return JsonResponse
     */
    public function saveEnvelopAfterHelloSignAction(Request $request, $userDocumentId) {
        /** @var UserDocument $document */
        $userDocument = $this->em->getRepository(UserDocument::class)->find($userDocumentId);

        list($result, $message) = $this->checkUserDocument($userDocument);

        if ($result) {
            /** @var Erp\UserBundle\Mailer\Processor $mailerService */
            $mailerService = $this->get('erp_user.mailer.processor');
            
            /** @var Erp\SignatureBundle\Service\HelloSignService $helloSignService */
            $helloSignService = $this->get('erp.signature.hellosign.service');
            
            // it's a landlord / tenant
            if (!($helloSignService->isFromUserSigning($this->getUser(), $userDocument))) {
                $userDocument->setStatus(UserDocument::STATUS_PENDING);
                
                // try to send the email to the manager
                if ($mailerService->sendAcceptedDocumentEmail($userDocument)) {
                    $message .= sprintf(' An email has been sent to %s to alert that you signed the requested document.', $userDocument->getFromUser()->getEmail());
                } else {
                    $message .= sprintf(' Unable to send the email %s for alerting about your signature of the requested document.', $userDocument->getFromUser()->getEmail());
                }
            }
            
            // check if all the requested users have signed the document
            $numOfSignatures = $userDocument->getNumOfSignatures() + 1;
            $userDocument->setNumOfSignatures($numOfSignatures);
            
            // all the requested users have signed
            if ($numOfSignatures == $userDocument->getMaxNumOfSignatures()) {
                $userDocument->setStatus(UserDocument::STATUS_COMPLETED);
                
                // send the email with the link of signed PDF file which could be downloaded
                $result = $helloSignService->getPdfLink($userDocument->getEnvelopIdToUser());
                if ($mailerService->sendSignedDocumentEmail($userDocument, $result)) {
                    if ($userDocument->getFromUser()) {
                        $message .= sprintf(' An email has been sent to %s and %s with a link to download the signed PDF.', $userDocument->getFromUser()->getEmail(), $userDocument->getToUser()->getEmail());
                    } else {
                        $message .= sprintf(' An email has been sent to %s with a link to download the signed PDF.', $userDocument->getToUser()->getEmail());
                    }
                } else {
                    $message .= ' Unable to send the email for downloading the signed PDF. File could not be available yet.';
                }
            }
            
            $this->em->persist($userDocument);
            $this->em->flush();

            return new JsonResponse(array('message' => $message, 'status' => $userDocument->getStatus()), Response::HTTP_OK);
        } else {
            return new JsonResponse($message, Response::HTTP_NOT_FOUND);
        }
    }
    
    /**
     * Create a new HelloSign template, editing an uploaded document
     * 
     * @param integer $userDocumentId
     * @return JsonResponse
     */
    public function manageTemplateHelloSignAction($userDocumentId) {
        /** @var UserDocument $document */
        $userDocument = $this->em->getRepository(UserDocument::class)->find($userDocumentId);

        list($result, $message) = $this->checkUserDocument($userDocument);
        if ($result) {
            try {
                $response = $this->get('erp.signature.hellosign.service')->manageTemplateRequest($userDocument);
                
                if (is_null($userDocument->getHelloSignTemplate())) {
                    $userDocument->setHelloSignTemplate($response->getId());
                    $this->em->flush();
                }
                
                // $isEmbeddedDraft = $response->isEmbeddedDraft();
                
                $data = array(
                    'TEMPLATE_URL' => $response->getEditUrl(),
                    'CLIENT_ID' => $this->getParameter('hellosign_app_clientid'),
                    'TEST_ENV' => $this->getParameter('hellosign_app_testenv')
                );
                
                return new JsonResponse($data, Response::HTTP_OK);
            } catch (\Exception $ex) {
                return new JsonResponse(array('error' => $ex->getMessage()), $ex->getCode());
            }
        } else {
            return new JsonResponse(array('error' => $message), Response::HTTP_NOT_FOUND);
        }
    }
    
    /**
     * 
     * @param integer $userDocumentId
     * @return JsonResponse
     */
    public function removeTemplateHelloSignAction($userDocumentId) {
        /** @var UserDocument $document */
        $userDocument = $this->em->getRepository(UserDocument::class)->find($userDocumentId);

        list($result, $message) = $this->checkUserDocument($userDocument);
        if ($result) {
            $this->get('erp.signature.hellosign.service')->deleteTemplate($userDocument);
            
            $templateId = $userDocument->getHelloSignTemplate();
            
            $userDocument->setHelloSignTemplate(null);
            $this->em->flush();
            
            return new JsonResponse(sprintf('Successfully removed templateId: %s', $templateId), Response::HTTP_OK);
        } else {
            return new JsonResponse(array('error' => $message), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Create ESign form
     *
     * @param Document $document
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createESignForm(Document $document) {
        $type = new ESignFormType();
        $action = $this->generateUrl('erp_esign_form', ['documentId' => $document->getId()]);

        $formOptions = ['action' => $action, 'method' => 'POST'];
        $form = $this->createForm($type, null, $formOptions);

        return $form;
    }

    /**
     * 
     * @param UserDocument $userDocument
     * @return array
     */
    protected function checkUserDocument(UserDocument $userDocument) {
        $result = true;
        $message = '';

        if (!$userDocument || !$userDocument->getDocument()) {
            $result = false;
            $message = 'Document not found';
        }

        if ($userDocument->isSigned()) {
            $result = false;
            $message = 'Document already signed';
        }

        return array($result, $message);
    }

}
