<?php
// app/modules/Interest/Routes/interest.routes.php

$router->get( '/interests',           'InterestController',  'index');
$router->get( '/interests/sent',      'InterestController',  'sent');
$router->get( '/interests/received',  'InterestController',  'received');
$router->post('/interest/send',       'InterestController',  'send');
$router->post('/interest/respond',    'InterestController',  'respond');
$router->post('/interest/cancel',     'InterestController',  'cancel');
