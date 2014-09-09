# KayueWordpressBundle

Improved version of the original [WordpressBundle](https://github.com/kayue/WordpressBundle). The biggest different is this new KayueWordpressBundle won't load the entire WordPress core, thus all the WordPress template funtions won't be available in your Symfony app. This is also the goal of the bundle; do everything in Symfony's way.

I started that bundle two years ago and the original repository grew somewhat chaotically, so I decided to start fresh with new repositories.

[![Build Status](https://travis-ci.org/kayue/KayueWordpressBundle.png?branch=master)](https://travis-ci.org/kayue/KayueWordpressBundle)

#### Features

* WordPress authentication (v1.0.0)
* Custom table prefix (v1.0.1)
* WordPress entities (v1.0.2)
* [Multisite](http://codex.wordpress.org/Create_A_Network) support (v1.1.0)
* Twig extension (v1.1.0)
* WordPress style shortcode (v1.1.0)

#### Todo

* Unit test (please help!)
* Port more WordPress shortcode over
* Refactor blog manager's code

## Installation

#### Composer

Add the bundle to `composer.json`

```json
{
    "require": {
        "kayue/kayue-wordpress-bundle": "1.1.*"
    }
}
```

Update Composer dependency:

```
composer update kayue/kayue-wordpress-bundle
```

#### Register the bundle

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Kayue\WordpressBundle\KayueWordpressBundle(),
    );
    // ...
}
```

## Configuration

#### Doctrine

This bundle requrie database connection. Make sure you have Doctrine configurated properly.

```yaml
// app/config/parameters.yml

parameters:
    database_driver:   pdo_mysql
    database_host:     127.0.0.1
    database_port:     ~
    database_name:     my_wordpress_db
    database_user:     root
    database_password: pass
```

#### config.yml

Set `site_url`, `logged_in_key` and `logged_in_salt` in your `config.yml`:

```yaml
kayue_wordpress:
    # Site URL must match *EXACTLY* with WordPress's setting. Can be found
    # on the Settings > General screen, there are field named "WordPress Address"
    site_url:       'http://localhost/wordpress'

    # Logged in key and salt. Can be found in the wp-config.php file.
    logged_in_key:  ':j$_=(:l@8Fku^U;MQ~#VOJXOZcVB_@u+t-NNYqmTH4na|)5Bhs1|tF1IA|>tz*E'
    logged_in_salt: ')A^CQ<R:1|^dK/Q;.QfP;U!=J=(_i6^s0f#2EIbGIgFN{,3U9H$q|o/sJfWF`NRM'

    # Optional: WordPress cookie path / domain settings.
    cookie_path:    '/'
    cookie_domain:  null

    # Optional: Custom table prefix. Default is "wp_".
    table_prefix:   'wp_'

    # Optional: Entity manager configuration to use (cache etc). Default is 'default'.
    entity_manager: 'default'
```

## Usage

An example to obtain post content, author, comments and categories:

```php
<?php
// path/to/controller.php

public function postAction($slug)
{
    $repo = $this->getDoctrine()->getRepository('KayueWordpressBundle:Post');
    $post = $repo->findOneBy(array(
        'slug'   => 'hello-world',
        'type'   => 'post',
        'status' => 'publish',
    ));

    echo $post->getTitle() , "\n";
    echo $post->getUser()->getDisplayName() , "\n";
    echo $post->getContent() , "\n";

    foreach($post->getComments() as $comment) {
        echo $comment->getContent() . "\n";
    }

    foreach($post->getTaxonomies()->filter(function(Taxonomy $tax) {
        // Only return categories, not tags or anything else.
        return 'category' === $tax->getName();
    }) as $tax) {
        echo $tax->getTerm()->getName() . "\n";
    }

    // ...
}
```

### Twig Extension

This bundle comes with the following Twig extensions.

#### Functions

* `wp_switch_blog` - equivalent to WordPress's `switch_to_blog()` method.
* `wp_find_attachments_by_post`
* `wp_find_one_attachment_by_id`
* `wp_find_featured_image_by_post` - equivalent to WordPress's `get_the_post_thumbnail` method.
* `wp_find_post_thumbnail` - alias of `wp_find_featured_image_by_post`
* `wp_find_one_option_by_name` - equivalent to WordPress's `get_option()` method.
* `wp_find_one_post_by_id`
* `wp_find_one_post_by_slug`
* `wp_find_all_metas_by_post` - equivalent to WordPress's `get_post_meta()` method.
* `wp_find_one_meta_by` - equivalent to WordPress's `get_post_meta()` method.
* `wp_find_metas_by` - equivalent to WordPress's `get_post_meta()` method.
* `wp_find_terms_by_post`
* `wp_find_categories_by_post` - equivalent to WordPress's `get_categories()` method.
* `wp_find_tags_by_post` - equivalent to WordPress's `get_tags()` method.

#### Filters

* `wp_autop` - Wrap paragraph with `<p>` tag. Needed for post formatting.
* `wp_texturize` - [Texturize](http://codex.wordpress.org/How_WordPress_Processes_Post_Content#Texturize). Needed for post formatting
* `wp_shortcode` - equivalent to WordPress's `do_shortcode()` method.
* `wp_extra` - [You can add some extra transformations easily](#Extra%20transformations)

### Multisite

Multisite is a feature of WordPress that allows multiple virtual sites to share a single WordPress installation. In this bundle, each blog (site) has its own entity manager. You need to use blog manager to retrive the blog and then the entity manager.

The following example shows you how to display the latest 10 posts in blog 2.

```php
<?php

public function firstPostAction()
{
    $blogManager = $this->get('kayue_wordpress.blog.manager');

    // Method 1: Switch current blog's id. Similar to WordPress's `switch_to_blog()` method.
    // Changing the current blog ID will affect Twig extensions too.
    $blogManager->setCurrentBlogId(2);
    $this->get('kayue_wordpress.post.manager')->findOnePostById(1);

    // Method 2: Use entity manager if you don't want to switch the entire blog.
    // This won't change the current blog ID.
    $anotherBlog = $blogManager->findBlogById(3)->getEntityManager();
    $posts = $anotherBlog->getRepository('KayueWordpressBundle:Post')->findOneById(1);
}
```

### WordPress Authentication

This bundle allow you to create a WordPress login form in Symfony. All you have to do is to configure the WordPress firewall in your `security.yml`.

The following example demonstrates how to turn AcmeDemoBundle's login form into a WordPress login form.

```yaml
security:

    encoders:
        # Add the WordPress password encoder
        Kayue\WordpressBundle\Entity\User:
            id: kayue_wordpress.security.encoder.phpass

    providers:
        # Add the WordPress user provider
        wordpress:
            entity: { class: Kayue\WordpressBundle\Entity\User, property: username }

    firewalls:
        login:
            pattern:  ^/demo/secured/login$
            security: false
        secured_area:
            pattern:    ^/demo/secured/
            # Add the WordPress firewall. Allow you to read WordPress's login state in Symfony app.
            kayue_wordpress: ~
            # Optional. Symfony's default form login works for WordPress user too.
            form_login:
                 check_path: /demo/secured/login_check
                 login_path: /demo/secured/login
                 default_target_path: /demo/secured/hello/world
            # Optional. Use this to logout.
            logout:
                path:   /demo/secured/logout
                target: /demo/secured/login
            # ...
```

### Shortcode

WordpressBundle support WordPress style shortcode. At the moment the bundle only come with the `[caption]` shortcode.
Pull request is welcome.

To create new shortcode, you need to

1. extends `ShortcodeInterface`
2. tag it with `kayue_wordpress.shortcode`

```php
<?php

use Kayue\WordpressBundle\Wordpress\Shortcode\ShortcodeInterface;

class GalleryShortcode implements ShortcodeInterface
{
    public function getName()
    {
        return 'gallery';
    }

    public function($attr, $content = null)
    {
        // do your things...

        return "<p>Return HTML</p>";
    }
}
}
```

```yaml
services:
    acme_demo_bundle.wordpress.shortcode.gallery:
        class: Acme\DemoBundle\Wordpress\Shortcode\GalleryShortcode
        tags:
            - { name: kayue_wordpress.shortcode }
```

### Extra transformations

WordpressBundle provide an easy way to add more transformations to your blogpost, as wordpress can do.

For now the only transformation is for videos links.

#### Configure transformers

You can enable or not transformers by using the configuration. The default configuration will enable every
transformers. You can disable a transformer by setting its configuration at `false`:

```yaml
kayue_wordpress:
    extra_transformers:
        video: false
```

Here is the reference of options available for various transformers:

```yaml

kayue_wordpress:
    extra_transformers:
        video:
            width: 100%
            height: 400
            allowfullscreen: true
            types: ['youtube', 'vimeo']
```

#### Add your own transformer

You can create a transformer and register it with the tag `kayue_wordpress.extra_transformer` and an alias.

```php
<?php

// YourProject\Wordpress\Transformer\ImageTransformer

use Kayue\WordpressBundle\Wordpress\ExtraTransformer\ExtraTransformerInterface;

class ImageTransformer implements ExtraTransformerInterface
{
    public function transform($content)
    {
        // transform the content and return it
        return $content;
    }
}
```

```yaml
# YourBundle/Resources/config/services.yml
services:
    your_project.extra_transformer.video:
        class: Kayue\WordpressBundle\Wordpress\Extra\VideoExtraTransformer
        tags:
            - { name: kayue_wordpress.extra_transformer, alias: videos }
```

### Doctrine Schema and Migrations

In order to use wordpress side-by-side with Doctrine, you will most likely want
to configure doctrine to ignore the wordpress tables so that it will not try to drop
or modify them when you run the ```doctrine:schema:update``` command. If you are using
data migrations in doctrine, you will run into similar issues when generating
migrations because doctrine will want to drop the wordpress tables. To avoid this problem,
you can tell doctrine to ignore the wordpress tables using the ```schema_filter``
configuration option. For example if your wordpress tables start with wp_ you can use
the following configuration.

```yaml
doctrine:
    dbal:
        schema_filter: ~^(?!wp_)~
```

More Information: http://symfony.com/doc/current/bundles/DoctrineMigrationsBundle/index.html#manual-tables

### Doctrine Fixtures

If you are using fixtures with doctrine, the ``doctrine:fixtures:load`` command will
truncate all of your wordpress tables by default. This is because the fixtures command
asks the default doctrine entity manager for all of the metadata for any mapped entities.
You can avoid this by creating a separate entity manager for this bundle to use.

```yaml
doctrine:
    default_entity_manager: default
    entity_managers:
        default:
            mappings:
                MyBundle: ~
                AnotherBundle: ~
        wordpress:
            mappings:
                KayueWordpressBundle: ~

kayue_wordpress:
    entity_manager: wordpress
```

More Information: http://symfony.com/doc/current/cookbook/doctrine/multiple_entity_managers.html
