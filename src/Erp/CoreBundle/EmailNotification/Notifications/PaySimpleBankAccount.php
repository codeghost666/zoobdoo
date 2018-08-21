<?php

namespace Erp\CoreBundle\EmailNotification\Notifications;

use Erp\CoreBundle\EmailNotification\AbstractEmailNotification;
use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;

class PaySimpleBankAccount extends AbstractEmailNotification
{
    /**
     * @var string
     */
    protected $type = EmailNotificationFactory::TYPE_PS_BANK_ACCOUNT;

    /**
     * Send email notification
     *
     * @param array $params
     */
    public function sendEmailNotification($params)
    {
        $message = $params['mailer']
            ->createMessage()
            ->setFrom([$params['mailFrom'] => 'Zoobdoo'])
            ->setTo($params['sendTo'])
            ->setContentType("text/html");

        $subject = 'Zoobdoo - Manager Contact';
        $template = 'ErpCoreBundle:EmailNotification:' . $this->type . '.html.twig';

        $emailParams['customerName'] = $params['customerName'];
        $emailParams['routingNumber'] = $params['routingNumber'];
        $emailParams['accountNumber'] = $params['accountNumber'];
        $emailParams['bankName'] = $params['bankName'];
        $emailParams['customerEmail'] = $params['customerEmail'];
        $emailParams['imageErp'] = $message->embed($this->getLogoPath($params));

        $message
            ->setSubject($subject)
            ->setBody($params['container']->get('templating')->render($template, $emailParams));
        $result = $params['mailer']->send($message);

        return $result;
    }
}
