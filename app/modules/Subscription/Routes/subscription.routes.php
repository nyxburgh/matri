<?php
// app/modules/Subscription/Routes/subscription.routes.php

$router->get( '/subscription',              'SubscriptionController',  'index');
$router->get( '/subscription/checkout/:plan_id', 'SubscriptionController', 'checkout');
$router->post('/subscription/initiate',     'SubscriptionController',  'initiate');
$router->post('/subscription/verify',       'SubscriptionController',  'verify');
$router->get( '/subscription/success',      'SubscriptionController',  'success');
$router->get( '/subscription/history',      'SubscriptionController',  'history');
