<?php

namespace Kayue\WordpressBundle\Model;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\Container;

class AttachmentManager extends AbstractManager implements AttachmentManagerInterface
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
     * @var PostMetaManager
     */
    protected $postMetaManager;

    /**
     * Constructor.
     *
     * @param Container     $container
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->em         = $this->getEntityManager();
        $this->repository = $this->em->getRepository('KayueWordpressBundle:Post');
        $this->postMetaManager = new PostMetaManager($container);
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

        return $this->postsToAttachments($posts);
    }

    /**
     * @param  integer $post
     * @return Attachment[]
     */
    public function findAttachmentsByPostId($post)
    {
        $posts = $this->repository->findBy(array(
            'parent' => $this->repository->find($post),
            'type'   => 'attachment',
        ));

        return $this->postsToAttachments($posts);
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

        return new Attachment($post);
    }

    /**
     * @param  array  $ids array of id of attachment
     * @param  string $order
     * @return \Kayue\WordpressBundle\Entity\Post[]
     */
    public function findImageWithIds(array $ids, $order = null)
    {
        $qb = $this->repository->createQueryBuilder('i');

        $qb
            ->where($qb->expr()->eq('i.type', ':type'))
            ->andWhere($qb->expr()->in('i.id', ':ids'))
        ;

        if ($order !== null) {
            $qb->orderBy('id', $order);
        }

        $qb
            ->setParameter('type', 'attachment')
            ->setParameter('ids', $ids)
        ;

        return $this->postsToAttachments($qb->getQuery()->execute());
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
     * @param int $id
     * @return array|null
     */
    public function getCurrentPreviousAndNextAttachment($id)
    {
        $current = $this->findOneAttachmentById($id);

        if (!$current) {
            return null;
        }

        /** @var \Kayue\WordpressBundle\Model\Attachment $images */
        $images = $this->findAttachmentsByPost($current->getParent());

        $last = null;
        $before = null;
        $after = null;
        foreach ($images as $image) {
            if ($last !== null && $image->getId() === $current->getId()) {
                $before = $last;
            }
            if ($last !== null && $last->getId() === $current->getId()) {
                $after = $image;
            }

            $last = $image;
        }

        return array('before' => $before, 'current' => $current, 'after' => $after, 'all' => $images);
    }

    /**
     * @param Post  $post
     *
     * @return Attachment
     */
    public function findFeaturedImageByPost(Post $post)
    {
        $featuredImageId = $this->postMetaManager->findOneMetaBy(array(
            'post' => $post,
            'key'  => '_thumbnail_id'
        ));

        if (!$featuredImageId) return null;

        return $this->findOneAttachmentById($featuredImageId->getValue());
    }

    /**
     * @param Post[] $posts
     * @return Attachment[]
     */
    private function postsToAttachments(array $posts)
    {
        $res = array();

        foreach ($posts as $post) {
            $res[] = new Attachment($post);
        }

        return $res;
    }
}