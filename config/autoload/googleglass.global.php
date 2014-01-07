<?php 

return array(
    'googleglass' => array(
        'applicationName' => "Test App",
        'development' => (APPLICATION_ENV == "development"),
        'subscriptionController' => 'Application\Controller\GlassSubscriptions'
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\GlassSubscriptions' => 'Application\Controller\GlassSubscriptionController'
         )
    )
);
