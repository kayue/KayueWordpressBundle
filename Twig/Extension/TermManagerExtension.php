<?php

namespace Kayue\WordpressBundle\Twig\Extension;

use Kayue\WordpressBundle\Model\TermManagerInterface;

class TermManagerExtension extends \Twig_Extension
{
    protected $termManager;

    public function __construct(TermManagerInterface $termManager)
    {
        $this->termManager = $termManager;
    }

    public function getName()
    {
        return "term_manager";
    }

    public function getFunctions()
    {
        return array(
            'wp_find_terms_by_post' => new \Twig_Function_Method($this, 'findTermsByPost'),
        );
    }

    public function findTermsByPost($id)
    {
        return $this->termManager->findTermsByPost($id);
    }

}
