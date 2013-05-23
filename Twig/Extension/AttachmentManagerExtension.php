<?php

namespace Kayue\WordpressBundle\Twig\Extension;

use Kayue\WordpressBundle\Entity\Post;
use Kayue\WordpressBundle\Model\AttachmentManagerInterface;

class AttachmentManagerExtension extends \Twig_Extension
{
    protected $attachmentManager;

    public function __construct(AttachmentManagerInterface $attachmentManager)
    {
        $this->attachmentManager = $attachmentManager;
    }

    public function getName()
    {
        return "attachment_manager";
    }

    public function getFunctions()
    {
        return array(
            'wp_find_attachments_by_post' => new \Twig_Function_Method($this, 'findAttachmentsByPost'),
            'wp_find_one_attachment_by_id' => new \Twig_Function_Method($this, 'findOneAttachmentById'),
            'wp_find_feature_image_by_post' => new \Twig_Function_Method($this, 'findFeatureImageByPost')
        );
    }

    public function findAttachmentsByPost(Post $post)
    {
        return $this->attachmentManager->findAttachmentsByPost($post);
    }

    public function findOneAttachmentById($id)
    {
        return $this->attachmentManager->findOneAttachmentById($id);
    }

    public function findFeatureImageByPost(Post $post, $size = null)
    {
        return $this->attachmentManager->findFeatureImageByPost($post, $size);
    }
}
