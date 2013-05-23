<?php

namespace Kayue\WordpressBundle\Twig\Extension;

use Doctrine\ORM\EntityManager;
use Kayue\WordpressBundle\Entity\Post;
use Kayue\WordpressBundle\Model\AttachmentManager;
use Kayue\WordpressBundle\Model\BlogManager;
use Kayue\WordpressBundle\Model\OptionManager;
use Kayue\WordpressBundle\Model\PostManager;
use Kayue\WordpressBundle\Model\PostMetaManager;
use Kayue\WordpressBundle\Model\TermManager;
use Symfony\Component\DependencyInjection\Container;

class WordpressExtension extends \Twig_Extension
{
    protected $em;

    protected $attachmentManager;
    protected $blogManager;
    protected $optionManager;
    protected $postManager;
    protected $postMetaManager;
    protected $termManager;

    public function __construct(EntityManager $em, Container $container)
    {
        $this->em = $em;
        $this->attachmentManager = new AttachmentManager($em);
        $this->blogManager = new BlogManager($container);
        $this->optionManager = new OptionManager($em);
        $this->postManager = new PostManager($em);
        $this->postMetaManager = new PostMetaManager($em);
        $this->termManager = new TermManager();
    }

    public function getName()
    {
        return "wordpress";
    }

    public function getFunctions()
    {
        return array(
            'wp_find_attachments_by_post' => new \Twig_Function_Method($this, 'findAttachmentsByPost'),
            'wp_find_one_attachment_by_id' => new \Twig_Function_Method($this, 'findOneAttachmentById'),
            'wp_find_feature_image_by_post' => new \Twig_Function_Method($this, 'findFeatureImageByPost'),
            'wp_find_blog_by_id' => new \Twig_Function_Method($this, 'findBlogById'),
            'wp_find_one_option_by_name' => new \Twig_Function_Method($this, 'findOneOptionByName'),
            'wp_find_post_by_id' => new \Twig_Function_Method($this, 'findPostById'),
            'wp_find_post_by_slug' => new \Twig_Function_Method($this, 'findPostBySlug'),
            'wp_find_all_metas_by_post' => new \Twig_Function_Method($this, 'findAllMetasByPost'),
            'wp_find_metas_by' => new \Twig_Function_Method($this, 'findMetasBy'),
            'wp_find_one_meta_by' => new \Twig_Function_Method($this, 'findOneMetaBy'),
            'wp_find_terms_by_post' => new \Twig_Function_Method($this, 'findTermsByPost'),
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

    public function findBlogById($id)
    {
        return $this->blogManager->findBlogById($id);
    }

    public function findOneOptionByName($id)
    {
        return $this->optionManager->findOneOptionByName($id);
    }

    public function findPostById($id)
    {
        return $this->postManager->findPostById($id);
    }

    public function findPostBySlug($slug)
    {
        return $this->postManager->findPostBySlug($slug);
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

    public function findTermsByPost($id)
    {
        return $this->termManager->findTermsByPost($id);
    }
}
