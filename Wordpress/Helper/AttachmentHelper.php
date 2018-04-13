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

    /**
     * AttachmentHelper constructor.
     *
     * @param ManagerRegistry $managerRegistry
     */
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
        // Switch to correct blog
        $originBlogId = $this->getManager()->getBlogId();
        if ($originBlogId !== $post->getBlogId()) {
            $this->managerRegistry->setCurrentBlogId($post->getBlogId());
        }

        $id = $this->getManager()->getRepository('KayueWordpressBundle:PostMeta')->findOneBy([
            'post' => $post,
            'key' => '_thumbnail_id',
        ]);

        if (!$id) {
            return null;
        }

        $thumbnail = $this->getManager()->getRepository('KayueWordpressBundle:Post')->findOneBy([
            'id' => $id->getValue(),
            'type' => 'attachment',
        ]);

        // Reset blog ID
        $this->managerRegistry->setCurrentBlogId($originBlogId);

        return $thumbnail;
    }

    public function getAttachmentUrl(Post $post, $size = 'post-thumbnail')
    {
        // Switch to correct blog
        $originBlogId = $this->getManager()->getBlogId();
        if ($originBlogId !== $post->getBlogId()) {
            $this->managerRegistry->setCurrentBlogId($post->getBlogId());
        }

        $metadata = $this->getManager()->getRepository('KayueWordpressBundle:PostMeta')->findOneBy([
            'post' => $post,
            'key' => '_wp_attachment_metadata',
        ]);

        // Reset blog ID
        $this->managerRegistry->setCurrentBlogId($originBlogId);

        if (!$metadata) {
            return null;
        }

        $metadata = $metadata->getValue();

        if (isset($metadata['sizes'][$size])) {
            return dirname($metadata['file']) . '/' . $metadata['sizes'][$size]['file'];
        }

        return null;
    }

    public function getAttachmentAltText(Post $post)
    {
        // Switch to correct blog
        $originBlogId = $this->getManager()->getBlogId();
        if ($originBlogId !== $post->getBlogId()) {
            $this->managerRegistry->setCurrentBlogId($post->getBlogId());
        }

        try {

            $metadata = $this->getManager()->getRepository('KayueWordpressBundle:PostMeta')->findOneBy([
                'post' => $post,
                'key' => '_wp_attachment_image_alt',
            ]);

        } catch (\Exception $exception) {

            return '';
        }

        // Reset blog ID
        $this->managerRegistry->setCurrentBlogId($originBlogId);

        if (!$metadata) {
            return null;
        }

        $altText = $metadata->getValue();

        return !is_null($altText) ? $altText : '';
    }

}
