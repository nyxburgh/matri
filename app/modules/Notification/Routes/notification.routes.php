<?php
// app/modules/Notification/Routes/notification.routes.php

$router->get( '/notifications',          'NotificationController',  'index');
$router->post('/notifications/read',     'NotificationController',  'markRead');
$router->post('/notifications/read-all', 'NotificationController',  'markAllRead');
$router->get( '/notifications/count',    'NotificationController',  'count');  // AJAX
