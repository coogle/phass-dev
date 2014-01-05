<?php

return array(
    'controller_plugins' => array(
       'invokables' => array(
           'Glass' => 'GoogleGlass\Mvc\Controller\Plugin\Glass',
           'Params' => 'GoogleGlass\Mvc\Controller\Plugin\Params'
        )
    ),
    'router' => array(
        'routes' => array(
            'googleglass-oauth2-callback' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/googleglass/oauth2callback',
                    'defaults' => array(
                        'controller' => 'GoogleGlass\Controller\Callback',
                        'action'     => 'OAuth2Callback',
                    ),
                )
            ),
            'googleglass-subscription-callback' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/googleglass/subscription',
                    'defaults' => array(
                        'controller' => 'GoogleGlass\Controller\Callback',
                        'action'     => 'subscriptionCallback',
                    ),
                )
            ),
            'googleglass-attachment-proxy-get' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/googleglass/attachment[/:itemId][/:attachmentId]',
                    'defaults' => array(
                        'controller' => 'GoogleGlass\Controller\Attachment',
                        'action' => 'get'
                    )
                )
            )
        )
    ),
    'googleglass' => array(
        'tokenStore' => 'GoogleGlass\OAuth2\Storage\Session',
        'auth' => array(
            "client_id" => null,
            "client_secret" => null,
            "auth_uri" => "https://accounts.google.com/o/oauth2/auth",
            "token_uri" => "https://accounts.google.com/o/oauth2/token",
            "redirect_uri" => null,
            "credentials_in_request_body" => true,
            'scopes' => array(
                'https://www.googleapis.com/auth/glass.timeline',
                'https://www.googleapis.com/auth/glass.location',
                'https://www.googleapis.com/auth/userinfo.profile'
            ),
        ),
        'httpClient' => array(
            'maxredirects'    => 5,
            'strictredirects' => false,
            'useragent'       => 'GoogleGlass Client',
            'timeout'         => 10,
            'adapter'         => 'Zend\Http\Client\Adapter\Curl',
            'httpversion'     => \Zend\Http\Request::VERSION_11,
            'storeresponse'   => true,
            'keepalive'       => false,
            'outputstream'    => false,
            'encodecookies'   => true,
            'argseparator'    => null,
            'rfc3986strict'   => false,
            'sslcapath'       => __DIR__ . "/certs/"
        ),
        'applicationName' => null,
        'subscriptionUri' => null,
        'randomKey' => 'KJ9#)NDIEOUEIJKL',
        'development' => true
    ),
    'controllers' => array(
        'invokables' => array(
            'GoogleGlass\Controller\Callback' => 'GoogleGlass\Controller\CallbackController',
            'GoogleGlass\Controller\Attachment' => 'GoogleGlass\Controller\AttachmentController'
        ),
    )
);