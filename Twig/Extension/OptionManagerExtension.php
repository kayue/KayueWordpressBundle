<?php

namespace Kayue\WordpressBundle\Twig\Extension;

use Kayue\WordpressBundle\Model\OptionManagerInterface;

class OptionManagerExtension extends \Twig_Extension
{
    protected $optionManager;

    public function __construct(OptionManagerInterface $optionManager)
    {
        $this->optionManager = $optionManager;
    }

    public function getName()
    {
        return "option_manager";
    }

    public function getFunctions()
    {
        return array(
            'wp_find_one_option_by_name' => new \Twig_Function_Method($this, 'findOneOptionByName'),
        );
    }

    public function findOneOptionByName($id)
    {
        return $this->optionManager->findOneOptionByName($id);
    }
}
