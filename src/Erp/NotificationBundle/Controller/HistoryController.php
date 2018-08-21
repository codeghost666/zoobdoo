<?php

namespace Erp\NotificationBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class HistoryController extends BaseController
{
    /**
     * @Template()
     */
    public function listAction(Request $request)
    {
        $user = $this->getUser();
        $historyItems = $this->getHistoryRepository()->getHistoryByUser($user);

        return [
            'historyItems' => $historyItems,
        ];
    }

    private function getHistoryRepository()
    {
        return $this->get('erp_notification.history_repository');
    }
}
