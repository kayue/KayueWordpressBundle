# KayueWordpressBundle

Improved version of the original [WordpressBundle](https://github.com/kayue/WordpressBundle). The biggest different is this new KayueWordpressBundle won't load the entire WordPress core, thus all the WordPress template funtions won't be available in your Symfony app. This is also the goal of the bundle; do everything in Symfony's way.

I started that bundle two years ago and the original repository grew somewhat chaotically, so I decided to start fresh with new repositories.

## Set Up

#### config.yml

Set `site_url`, `logged_in_key` and `logged_in_salt` to `config.yml`

```yaml
kayue_wordpress:
    # Site URL must match *EXACTLY* with WordPress's setting. Can be found
    # on the Settings > General screen, there are field named "WordPress Address"
    site_url:       'http://localhost/wordpress'

    # Logged in key and salt. Can be found in the wp-config.php file.
    logged_in_key:  ':j$_=(:l@8Fku^U;MQ~#VOJXOZcVB_@u+t-NNYqmTH4na|)5Bhs1|tF1IA|>tz*E'
    logged_in_salt: ')A^CQ<R:1|^dK/Q;.QfP;U!=J=(_i6^s0f#2EIbGIgFN{,3U9H$q|o/sJfWF`NRM'

    # WordPress cookie path / domain settings. (Optional)
    cookie_path:    '/'
    cookie_domain:  null

    # Table prefix. (Optional, default value is "wp_")
    table_prefix:   'wp_'
```

#### security.yml

Configure encoder, user provider, and firewall.

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

## Todo

* Complete WordPress entities
* OptionManager
* Multi-site support
