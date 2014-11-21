<?php

namespace Kayue\WordpressBundle\Wordpress\Shortcode;

use Symfony\Component\DependencyInjection\Container;

class GalleryShortcode implements ShortcodeInterface
{
    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param  array  $attr    Attributes of the shortcode
     * @param  string $content Content of the shortcode if it is composed of open tag and end tag
     * @return string A simple string containing html that will replace the shortcode
     */
    public function process(array $attr, $content = null)
    {
        $order   = null;
        if (isset($attr['order'])) {
            $order = strtolower($attr['order']) === 'desc' ? 'DESC' : 'ASC';
        }

        $manager = $this->container->get('kayue_wordpress.attachment.manager');

        $images = array();
        if (isset($attr['ids'])) {
            $images  = explode(',', $attr['ids']);
            $images  = $manager->findImageWithIds($images, $order);
        }
        if (isset($attr['id'])) {
            $images = $manager->findAttachmentsByPost($attr['id']);
        }

        return $this->createTemplateWith($images, $attr);
    }

    /**
     * @param  \Kayue\WordpressBundle\Entity\Post[] $images
     * @param  array                                $attr
     * @return string
     */
    private function createTemplateWith(array $images, array $attr)
    {
        $templating = $this->container->get('templating');

        $templateName = $templating instanceof \Symfony\Bundle\TwigBundle\TwigEngine ?
            'KayueWordpressBundle:Shortcode:gallery.html.twig' :
            'KayueWordpressBundle:Shortcode:gallery.html.php'
        ;

        return $templating->render($templateName, array(
            'images' => $images,
            'linkToFile' => isset($attr['link']) && $attr['link'] === 'file'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gallery';
    }
}
