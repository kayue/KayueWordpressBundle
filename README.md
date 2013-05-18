# KayueWordpressBundle

Improved version of the original [WordpressBundle](https://github.com/kayue/WordpressBundle). The biggest different is this new KayueWordpressBundle won't load the entire WordPress core, thus all the WordPress template funtions won't be available in your Symfony app. This is also the goal of the bundle; do everything in Symfony's way.

I started that bundle two years ago and the original repository grew somewhat chaotically, so I decided to start fresh with new repositories.

[![Build Status](https://travis-ci.org/kayue/KayueWordpressBundle.png?branch=master)](https://travis-ci.org/kayue/KayueWordpressBundle)

#### Features

* WordPress authentication (v1.0.0)
* Table prefix (v1.0.1)
* WordPress entities (v1.0.2)

## Installation

#### Composer

Add the bundle to `composer.json`

```json
{
    "require": {
        "kayue/kayue-wordpress-bundle": "v1"
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

    # Optional: Custom table prefix, default is "wp_".
    table_prefix:   'wp_'

    # Optional: Enable multi-site.
    sties:
        foo:            # site name, could be anything you want
            blog_id: 1  # blog id
        bar:
            blog_id: 2
```

#### security.yml

Configure encoder, user provider, and enable the WordPress firewall in your `security.yml`.

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

## Todo

* Add some Twig helper
* OptionManager
* Multi-site support
