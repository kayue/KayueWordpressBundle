<?php

namespace Kayue\WordpressBundle\Model;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Kayue\WordpressBundle\Entity\Post;

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

    /**
     * Constructor.
     *
     * @param EntityManager     $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em         = $em;
        $this->repository = $em->getRepository('KayueWordpressBundle:Post');
    }

    /**
     * @param Post $post
     *
     * @return AttachmentInterface[]
     */
    public function findAttachmentsByPost(Post $post)
    {
        // TODO: Convert posts to attachments
        return $this->repository->findBy(array(
            'parent' => $post,
            'type'   => 'attachment',
        ));
    }

    /**
     * @param $id integer
     *
     * @return AttachmentInterface[]
     */
    public function findOneAttachmentById($id)
    {
        return $this->repository->findOneBy(array(
            'id'     => $id,
            'type'   => 'attachment',
        ));
    }

    /**
     * @param Post  $post
     * @param array $size A 2-item array representing width and height in pixels, e.g. array(32,32).
     *
     * @return mixed
     */
    public function findFeaturedImageByPost(Post $post, $size = null)
    {
        // TODO: Implement findFeaturedImageByPost() method.
    }
}