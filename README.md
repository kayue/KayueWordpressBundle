#### config.yml

Set `logged_in_key` and `logged_in_salt`, can be found in wp-config.php.

```
kayue_wordpress:
    logged_in_key:  'isuhc5vD{1&fukZ[&k_#&K-Q.+-ipuS2BcWUn%lt?pg(.elS-b++j2>grL,;8EW/'
    logged_in_salt: '.oYy]!|SU/5= [.C5;0+,#t,u1+`_#l%d3)+Z!~~NBV0a81eAGD4k[ pxhnAhiy0'
```

#### security.yml

Configure encoder, user provider, and firewall.

```yml
security:
    encoders:
        Kayue\WordpressBundle\Entity\User:
            id: kayue_wordpress.security.encoder.phpass

    providers:
        wordpress:
            entity: { class: Kayue\WordpressBundle\Entity\User, property: username }

    firewalls:
        login:
            pattern:  ^/demo/secured/login$
            security: false

        secured_area:
            pattern:    ^/demo/secured/

            kayue_wordpress:
                name:   wordpress_logged_in_cc55b6db1c92ef7ec624e6e0c2005814
                domain: .hb.com

            form_login:
                 check_path: /demo/secured/login_check
                 login_path: /demo/secured/login
                 default_target_path: /demo/secured/hello/world

            logout:
                path:   /demo/secured/logout
                target: /demo/secured/login

            ...
```