The gallery shortcode
=====================

Wordpress provide a gallery using the "shortcode" `[gallery ids="12,565,52"]` in a blog post.

Our twig filter "wp_shortcode" will automatically transform it.

Basic usage
-----------

If you don't already did it, you have to register our routes in your application by adding theses lines to your `routing.yml` file.

```yaml
wordpress_bundle:
    resource: "@KayueWordpressBundle/Resources/config/routing.yml"
    prefix: /blog
```

> The bundle does support all options of the gallery shortcut. You can only use "id", "ids", "order", "link"

Overriding
----------

You probably want to personalize your gallery.

You can easily override the template used for the shortcode by adding a new template on this path:
`app/Resources/KayueWordpressBundle/views/Shortcode/gallery.html.twig`

If this preview is ok for you maybe you want to redefine the whole gallery.
You can simply not register our routing and define the route `kayue_wordpress_shortcode_gallery` with one of your controller.