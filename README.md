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
* Major code update. (v2.0.0)
* Support Symfony 4, new cache configuration (v4.0.0) 

#### Todo

* Unit test (please help!)

## Installation

#### Composer

```
composer require kayue/kayue-wordpress-bundle
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

The following configuration is optional.

```yaml
kayue_wordpress:
    # Custom table prefix. Default is "wp_".
    table_prefix:   'wp_'

    # Doctrine connection to use. Default is 'default'.
    connection: 'default'

    # Specify Symfony cache pool
    orm:
        metadata_cache_pool: cache.system
        query_cache_pool: cache.app
        result_cache_pool: cache.app
    
    # The following configuration only needed only when you use WordPress authentication. 
    
    # Site URL must match *EXACTLY* with WordPress's setting. Can be found
    # on the Settings > General screen, there are field named "WordPress Address"
    site_url:       'http://localhost/wordpress'

    # Logged in key and salt. Can be found in the wp-config.php file.
    logged_in_key:  ':j$_=(:l@8Fku^U;MQ~#VOJXOZcVB_@u+t-NNYqmTH4na|)5Bhs1|tF1IA|>tz*E'
    logged_in_salt: ')A^CQ<R:1|^dK/Q;.QfP;U!=J=(_i6^s0f#2EIbGIgFN{,3U9H$q|o/sJfWF`NRM'

    # WordPress cookie path / domain settings.
    cookie_path:    '/'
    cookie_domain:  null
```

## Usage

An example to obtain post content, author, comments and categories:

```php
<?php
// path/to/controller.php

public function postAction($slug)
{
    $repo = $this->get('kayue_wordpress')->getManager()->getRepository('KayueWordpressBundle:Post');
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
* `wp_find_option_by` - equivalent to WordPress's `get_option()` method.
* `wp_find_post_by` - Get post by ID or slug.
* `wp_find_post_metas_by($post, $key)` - equivalent to WordPress's `get_post_meta()` method.
* `wp_find_post_metas_by({'post': $post, 'key': $key})` - Same as above, accept array as argument.
* `wp_find_comments_by_post($post)` - return all approved comments in a post.
* `wp_find_attachments_by_post($post)`
* `wp_find_attachment_by_id($id)`
* `wp_find_thumbnail($post)` - alias of `wp_find_featured_image_by_post`
* `wp_find_featured_image` - equivalent to WordPress's `get_the_post_thumbnail` method.
* `wp_get_attachment_url($post)`
* `wp_get_post_format`
* `wp_find_terms_by_post`
* `wp_find_categories_by_post` - equivalent to WordPress's `get_categories()` method.
* `wp_find_tags_by_post` - equivalent to WordPress's `get_tags()` method.

#### Filters

* `wp_autop` - Wrap paragraph with `<p>` tag. Needed for post formatting.
* `wp_texturize` - [Texturize](http://codex.wordpress.org/How_WordPress_Processes_Post_Content#Texturize). Needed for post formatting
* `wp_shortcode` - equivalent to WordPress's `do_shortcode()` method.

> To transform extra content like video links or social network links, you can use the [Essence Bundle](https://github.com/kayue/KayueEssenceBundle)

### Multisite

Multisite is a feature of WordPress that allows multiple virtual sites to share a single WordPress installation. In this bundle, each blog (site) has its own entity manager. You need to use blog manager to retrive the blog and then the entity manager.

The following example shows you how to display the latest 10 posts in blog 2.

```php
<?php

public function firstPostAction()
{
    // Method 1: Switch current blog's id. Similar to WordPress's `switch_to_blog()` method.
    // Changing the current blog ID will affect Twig extensions too.
    $blogManager = $this->get('kayue_wordpress')->getManager();
    $blogManager->setCurrentBlogId(2);
    $this->getRepository('KayueWordpressBundle:Post')->findOnePostById(1);

    // Method 2: Use entity manager if you don't want to switch the entire blog.
    // This won't change the current blog ID.
    $blogId = 3;
    $anotherBlog = $this->get('kayue_wordpress')->getManager($blogId);
    $posts = $anotherBlog->getRepository('KayueWordpressBundle:Post')->findOneById(1);
}
```

### Shortcode

WordpressBundle support WordPress style shortcode. At the moment the bundle only come with the `[caption]` and `[gallery]` shortcode.
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
