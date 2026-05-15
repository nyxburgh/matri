<?php
// app/modules/Chat/Routes/chat.routes.php

$router->get( '/chat',               'ChatController',  'index');
$router->get( '/chat/:user_id',      'ChatController',  'thread');
$router->post('/chat/send',          'ChatController',  'send');
$router->get( '/chat/messages/:user_id', 'ChatController', 'poll');  // AJAX polling
$router->post('/chat/read/:user_id', 'ChatController',  'markRead');
