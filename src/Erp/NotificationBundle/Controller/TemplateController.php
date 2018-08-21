<?php

namespace Erp\NotificationBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Erp\NotificationBundle\Entity\Template;
use Erp\NotificationBundle\Form\Type\TemplateType;
use Erp\UserBundle\Entity\User;

class TemplateController extends BaseController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        $repository = $this->getDoctrine()->getManagerForClass(Template::class)->getRepository(Template::class);
        $templates = $repository->getTemplatesByUser($user);

        return $this->render('ErpNotificationBundle:Template:list.html.twig', [
            'templates' => $templates,
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $entity = new Template();
        $entity->setUser($user);

        return $this->update($entity, $request);
    }

    /**
     * @param Template $entity
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Template $entity, Request $request)
    {
        $this->checkAccess($entity);
        return $this->update($entity, $request);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAction($id)
    {
        $em = $this->getDoctrine()->getManagerForClass(Template::class);
        $repository = $em->getRepository(Template::class);

        /** @var Template $entity */
        $entity = $repository->find($id);
        $this->checkAccess($entity);

        $em->remove($entity);
        $em->flush();

        return $this->redirectToRoute('erp_notification_template_list');
    }

    /**
     * @param Template $entity
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function update(Template $entity, Request $request)
    {
        $form = $this->createForm(new TemplateType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManagerForClass(Template::class);
            $em->persist($entity);
            $em->flush();

            $this->addFlash(
                'alert_ok',
                'Success'
            );

            return $this->redirectToRoute('erp_notification_template_list');
        }

        return $this->render('ErpNotificationBundle:Template:create.html.twig', [
            'form' => $form->createView(),
            'entity' => $entity,
        ]);
    }

    /**
     * @param Template $entity
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function printPdfAction(Template $entity, Request $request)
    {
        $this->checkAccess($entity);

        $html = $this->getTemplateManager()->renderTeplate($entity);
        $fileName = 'notification_template ('.$entity->getTitle().').pdf';
        $pdf = $this->get('knp_snappy.pdf')->getOutputFromHtml($html);
        return $this->pdfResponse($pdf, $fileName);
    }

    private function getTemplateManager()
    {
        return $this->get('erp_notification.template_manager');
    }

    private function checkAccess(Template $template)
    {
        $user = $this->getUser();
        if ($user !== $template->getUser()) {
            throw $this->createAccessDeniedException('You don\'t have access to this Template');
        }
    }
}
