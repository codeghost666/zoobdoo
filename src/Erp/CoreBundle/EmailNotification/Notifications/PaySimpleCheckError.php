<?php

namespace Erp\CoreBundle\EmailNotification\Notifications;

use Erp\CoreBundle\EmailNotification\AbstractEmailNotification;
use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;

class PaySimpleCheckError extends AbstractEmailNotification
{
    /**
     * @var string
     */
    protected $type = EmailNotificationFactory::TYPE_PS_CHECK_ERROR;

    /**
     * Send email notification when new Administrator created
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

        $subject = 'Zoobdoo - An error has occurred during Payment operation!';
        $template = 'ErpCoreBundle:EmailNotification:' . $this->type . '.html.twig';

        $emailParams['customerName'] = $params['customerName'];
        $emailParams['customerId'] = $params['customerId'];
        $emailParams['imageErp'] = $message->embed($this->getLogoPath($params));

        $message
            ->setSubject($subject)
            ->setBody($params['container']->get('templating')->render($template, $emailParams));
        $result = $params['mailer']->send($message);

        return $result;
    }
}
