<?php

$routes['content/<type:[-\w]+>/<action:[-\w]+>/?'] = 'content/<action>';
$routes['directories/references/<type:[-\w]+>/<action:[-\w]+>/?'] = 'directories/references/<action>';
$routes['segment/<type:[-\w]+>/<action:[-\w]+>/?'] = 'segment/<action>';
$routes['<controller:\w+>/<action:\w+>/<id\d+>'] = '<controller>/<action>';
$routes['<folder:[\w-]+>/<controller:[\w-]+>/<action:[\w-]+>/<id\d+>'] = '<folder>/<controller>/<action>';

return $routes;