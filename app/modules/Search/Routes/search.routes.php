<?php
// app/modules/Search/Routes/search.routes.php

$router->get( '/search',             'SearchController',   'index');
$router->get( '/search/results',     'SearchController',   'results');
$router->get( '/search/advanced',    'SearchController',   'advanced');
