<?php

// don't change the values here, copy local.php.dist and remove the extension instead
return array(

    'enabled_logger' => array(
        'error_log'  => (getenv('DEVELOPMENT') === 'true') ? true : false,
    ),

    'emails' => array(),

    'gist_token' => '',

    'recaptcha' => array(
        'public'  => '',
        'private' => '',
    )
);
