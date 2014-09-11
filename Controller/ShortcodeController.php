<?php

namespace Kayue\WordpressBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ShortcodeController extends Controller
{
    /**
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function galleryAction($id)
    {
        $attachments = $this->get('kayue_wordpress.attachment.manager')->getCurrentPreviousAndNextAttachment($id);

        if ($attachments === null) {
            throw $this->createNotFoundException('Impossible to find the attachment');
        }

        return $this->render('KayueWordpressBundle:Shortcode:focus_gallery.html.twig', array(
            'current' => $attachments['current'],
            'before' => $attachments['before'],
            'after' => $attachments['after'],
            'all' => $attachments['all']
        ));
    }
}
