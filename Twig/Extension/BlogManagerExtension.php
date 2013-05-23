<?php

namespace Kayue\WordpressBundle\Twig\Extension;

use Kayue\WordpressBundle\Model\BlogManagerInterface;

class BlogManagerExtension extends \Twig_Extension
{
    protected $blogManager;

    public function __construct(BlogManagerInterface $blogManager)
    {
        $this->blogManager = $blogManager;
    }

    public function getName()
    {
        return "blog_manager";
    }

    public function getFunctions()
    {
        return array(
            'wp_find_blog_by_id' => new \Twig_Function_Method($this, 'findBlogById'),
        );
    }

    public function findBlogById($id)
    {
        return $this->blogManager->findBlogById($id);
    }
}
