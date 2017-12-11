<?php

namespace Kayue\WordpressBundle\Wordpress\Helper;

use Kayue\WordpressBundle\Entity\Post;
use Kayue\WordpressBundle\Wordpress\ManagerRegistry;

class AttachmentHelper
{
    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    protected function getManager()
    {
        return $this->managerRegistry->getManager();
    }

    public function findThumbnail(Post $post)
    {
        if ($this->getManager()->getBlogId() !== $post->getBlogId()) {
            $this->getManager()->setBlogId($post->getBlogId());
        }

        $id = $this->getManager()->getRepository('KayueWordpressBundle:PostMeta')->findOneBy([
            'post' => $post,
            'key' => '_thumbnail_id',
        ]);

        if (!$id) {
            return null;
        }

        return $this->getManager()->getRepository('KayueWordpressBundle:Post')->findOneBy([
            'id' => $id->getValue(),
            'type' => 'attachment',
        ]);
    }

    public function getAttachmentUrl(Post $post, $size = 'post-thumbnail')
    {
        if ($this->getManager()->getBlogId() !== $post->getBlogId()) {
            $this->getManager()->setBlogId($post->getBlogId());
        }

        $metadata = $this->getManager()->getRepository('KayueWordpressBundle:PostMeta')->findOneBy([
            'post' => $post,
            'key' => '_wp_attachment_metadata',
        ]);

        if (!$metadata) {
            return null;
        }

        $metadata = $metadata->getValue();

        if (isset($metadata['sizes'][$size])) {
            return dirname($metadata['file']) . '/' . $metadata['sizes'][$size]['file'];
        }

        return null;
    }
}
