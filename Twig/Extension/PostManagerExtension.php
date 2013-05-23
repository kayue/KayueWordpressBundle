<?php

namespace Kayue\WordpressBundle\Twig\Extension;

use Kayue\WordpressBundle\Model\PostManagerInterface;

class PostManagerExtension extends \Twig_Extension
{
    protected $postManager;

    public function __construct(PostManagerInterface $postManager)
    {
        $this->postManager = $postManager;
    }

    public function getName()
    {
        return "post_manager";
    }

    public function getFunctions()
    {
        return array(
            'wp_find_post_by_id' => new \Twig_Function_Method($this, 'findPostById'),
            'wp_find_post_by_slug' => new \Twig_Function_Method($this, 'findPostBySlug')
        );
    }

    public function findPostById($id)
    {
        return $this->postManager->findPostById($id);
    }

    public function findPostBySlug($slug)
    {
        return $this->postManager->findPostBySlug($slug);
    }
}
