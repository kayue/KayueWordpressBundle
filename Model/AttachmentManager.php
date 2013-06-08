<?php

namespace Kayue\WordpressBundle\Model;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class AttachmentManager implements AttachmentManagerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    protected $postMetaManager;

    /**
     * Constructor.
     *
     * @param EntityManager     $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em         = $em;
        $this->repository = $em->getRepository('KayueWordpressBundle:Post');
        $this->postMetaManager = new PostMetaManager($em);
    }

    /**
     * @param Post $post
     *
     * @return AttachmentInterface[]
     */
    public function findAttachmentsByPost(Post $post)
    {
        $posts = $this->repository->findBy(array(
            'parent' => $post,
            'type'   => 'attachment',
        ));

        $result = array();
        /** @var $post Post */
        foreach ($posts as $post) {
            /** @var $meta PostMeta */
            $meta = $this->postMetaManager->findOneMetaBy(array(
                'post' => $post,
                'key'  => '_wp_attachment_metadata'
            ));

            if ($meta) {
                $rawMeta = $meta->getValue();
                $attachment = new Attachment($post);

                $attachment->setUrl($rawMeta['file']);
                $attachment->setThumbnailUrl(substr($rawMeta['file'], 0, strrpos($rawMeta['file'], '/') + 1) . $rawMeta['sizes']['thumbnail']['file']);

                $result[] = $attachment;
            }
        }

        return $result;
    }

    /**
     * @param $id integer
     *
     * @return AttachmentInterface
     */
    public function findOneAttachmentById($id)
    {
        $post = $this->repository->findOneBy(array(
            'id'     => $id,
            'type'   => 'attachment',
        ));

        /** @var $meta PostMeta */
        $meta = $this->postMetaManager->findOneMetaBy(array(
            'post' => $post,
            'key'  => '_wp_attachment_metadata'
        ));

        if ($meta) {
            $rawMeta = $meta->getValue();
            $attachment = new Attachment($post);

            $attachment->setUrl($rawMeta['file']);
            $attachment->setThumbnailUrl(substr($rawMeta['file'], 0, strrpos($rawMeta['file'], '/') + 1) . $rawMeta['sizes']['thumbnail']['file']);
            return $attachment;
        }

        return null;
    }

    public function getAttachmentOfSize(Attachment $attachment, $size = null)
    {
        if($size === 'full') {
            return $attachment->getUrl();
        }

        /** @var $meta PostMeta */
        $meta = $this->postMetaManager->findOneMetaBy(array(
            'post' => $attachment,
            'key'  => '_wp_attachment_metadata'
        ));

        if (!$meta) {
            return null;
        }

        $rawMeta = $meta->getValue();

        $chosenSize = null;
        $min = 999999;
        foreach ($rawMeta['sizes'] as $meta) {
            if ($meta['width'] >= $size[0] && $meta['height'] >= $size[1]) {
                $dimensionDiff = $meta['width'] - $size[0] + $meta['height'] - $size[1];
                if ($dimensionDiff < $min) {
                    $chosenSize = $meta;
                    $min = $dimensionDiff;
                }
            }
        }

        if ($chosenSize) {
            return substr($rawMeta['file'], 0, strrpos($rawMeta['file'], '/') + 1) . $chosenSize['file'];
        } else {
            return $attachment->getUrl();
        }
    }

    /**
     * @param Post  $post
     * @param array $size A 2-item array representing width and height in pixels, e.g. array(32,32).
     *
     * @return mixed
     */
    public function findFeaturedImageByPost(Post $post, $size = null)
    {
        $featuredImageId = $this->postMetaManager->findOneMetaBy(array(
            'post' => $post,
            'key'  => '_thumbnail_id'
        ));

        if (!$featuredImageId) return null;

        $attachment = $this->findOneAttachmentById($featuredImageId->getValue());

        if (!$attachment) {
            return null;
        }

        return $this->getAttachmentOfSize($attachment, $size);
    }
}