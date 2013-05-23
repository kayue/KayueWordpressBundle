<?php

namespace Kayue\WordpressBundle\Twig\Extension;

use Kayue\WordpressBundle\Entity\Post;
use Kayue\WordpressBundle\Entity\PostMeta;
use Kayue\WordpressBundle\Model\PostMetaManagerInterface;

class PostMetaManagerExtension extends \Twig_Extension
{
    protected $postMetaManager;

    public function __construct(PostMetaManagerInterface $postMetaManager)
    {
        $this->postMetaManager = $postMetaManager;
    }

    public function getName()
    {
        return "post_meta_manager";
    }

    public function getFunctions()
    {
        return array(
            'wp_add_meta' => new \Twig_Function_Method($this, 'addMeta'),
            'wp_save_meta' => new \Twig_Function_Method($this, 'saveMeta'),
            'wp_delete_meta' => new \Twig_Function_Method($this, 'deleteMeta'),
            'wp_find_all_metas_by_post' => new \Twig_Function_Method($this, 'findAllMetasByPost'),
            'wp_find_metas_by' => new \Twig_Function_Method($this, 'findMetasBy'),
            'wp_find_one_meta_by' => new \Twig_Function_Method($this, 'findOneMetaBy'),
        );
    }

    public function addMeta(Post $post, PostMeta $meta)
    {
        return $this->postMetaManager->addMeta($post, $meta);
    }

    public function saveMeta(PostMeta $meta)
    {
        return $this->postMetaManager->saveMeta($meta);
    }

    public function deleteMeta(PostMeta $meta)
    {
        return $this->postMetaManager->deleteMeta($meta);
    }

    public function findAllMetasByPost(Post $post)
    {
        return $this->postMetaManager->findAllMetasByPost($post);
    }

    public function findMetasBy(array $criteria)
    {
        return $this->postMetaManager->findMetasBy($criteria);
    }

    public function findOneMetaBy(array $criteria)
    {
        return $this->postMetaManager->findOneMetaBy($criteria);
    }

}
