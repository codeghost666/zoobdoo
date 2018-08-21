<?php

namespace Erp\SignatureBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Erp\UserBundle\Entity\UserDocument;

class WebhookController extends Controller
{
    public function notifyAction(Request $request)
    {
        $content = $request->getContent();
        $xml = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_PARSEHUGE);
        $envelopeId = (string) $xml->EnvelopeStatus->EnvelopeID;
        $status = (string) $xml->EnvelopeStatus->Status;

        $em = $this->getDoctrine()->getManagerForClass(UserDocument::class);
        $repository = $em->getRepository(UserDocument::class);
        $userDocument = $repository->findOneBy(['envelopId' => $envelopeId]);

        switch ($status) {
            case UserDocument::STATUS_COMPLETED:
            case UserDocument::STATUS_SENT:
                $userDocument->setStatus((string)$xml->EnvelopeStatus->Status);

                try {
                    $em->persist($userDocument);
                    $em->flush();
                } catch (\Exception $e) {
                    $logger = $this->get('logger');
                    //TODO Create logger for docusign

                    return new Response('ok');
                }
                break;
        }

        return new Response('ok');
    }
}