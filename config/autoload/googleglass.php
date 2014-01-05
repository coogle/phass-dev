<?php 

return array(
    'googleglass' => array(
		'dbAdapter' => 'Zend\Db\Adapter\Adapter',
		'applicationName' => "Test App",
		'development' => (APPLICATION_ENV == "development")
    )
);
