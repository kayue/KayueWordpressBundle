<?php

namespace Kayue\WordpressBundle\Security\Http\Logout;

use Symfony\Component\Security\Http\Logout\CookieClearingLogoutHandler;

class WordpressCookieClearingLogoutHandler extends CookieClearingLogoutHandler
{
    public function __construct($siteUrl, $path, $domain)
    {
        parent::__construct(array(
            'wordpress_logged_in_'.md5($siteUrl) => array(
                'path'   => $path,
                'domain' => $domain,
            )
        ));
    }
}