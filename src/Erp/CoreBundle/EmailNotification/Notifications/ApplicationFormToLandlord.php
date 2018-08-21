<?php

namespace Erp\CoreBundle\EmailNotification\Notifications;

use Erp\CoreBundle\EmailNotification\AbstractEmailNotification;
use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;

class ApplicationFormToLandlord extends AbstractEmailNotification
{
    /**
     * @var string
     */
    protected $type = EmailNotificationFactory::TYPE_APPLICATION_FORM_TO_LANDLORD;

    /**
     * Send email notification when new Administrator created
     *
     * @param array $params
     */
    public function sendEmailNotification($params)
    {
        /** @var $contailner \Symfony\Component\DependencyInjection\ContainerInterface */
        $contailner = $params['container'];
        $eImg = $this->getLogoPath($params);
        $pdfFile = \Swift_Attachment::fromPath($params['pdfDir'] . '/' . $params['filename']);

        $message = $params['mailer']->createMessage()
            ->setFrom([$params['mailFrom'] => 'eRentPay'])
            ->setTo($params['sendTo'])
            ->setReplyTo($params['replyTo'])
            ->setContentType('text/html');

        $template = 'ErpCoreBundle:EmailNotification:' . $this->getType() . '.html.twig';
        $emailParams = [
            'url'  => $params['url'],
            'filename'  => $params['filename'],
            'pdfFile'   => $message->attach($pdfFile),
            'imageErp'  => $message->embed($eImg),
        ];

        $body = $contailner->get('templating')->render($template, $emailParams);
        $message->setSubject('eRentPay - Potential tenant has completed Rental Application')->setBody($body);
        $result = $params['mailer']->send($message);

        return $result;
    }
}
