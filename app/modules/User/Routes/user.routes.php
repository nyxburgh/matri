<?php
// app/modules/User/Routes/user.routes.php

// ── Home ─────────────────────────────────────────────────────────────────────
$router->get( '/',                  'UserDashboardController',  'home');

// ── Authentication ────────────────────────────────────────────────────────────
$router->get( '/login',             'UserAuthController',       'loginForm');
$router->post('/login',             'UserAuthController',       'loginPost');
$router->get( '/register',          'UserAuthController',       'registerForm');
$router->post('/register',          'UserAuthController',       'registerPost');
$router->get( '/verify-otp',        'UserAuthController',       'otpForm');
$router->post('/verify-otp',        'UserAuthController',       'otpVerify');
$router->post('/resend-otp',        'UserAuthController',       'resendOtp');
$router->get( '/logout',            'UserAuthController',       'logout');
$router->get( '/forgot-password',   'UserAuthController',       'forgotForm');
$router->post('/forgot-password',   'UserAuthController',       'forgotPost');

// ── Dashboard ─────────────────────────────────────────────────────────────────
$router->get( '/dashboard',         'UserDashboardController',  'index');
$router->get( '/dashboard/matches', 'UserDashboardController',  'matches');
$router->get( '/dashboard/views',   'UserDashboardController',  'profileViews');

// ── Account / Settings ────────────────────────────────────────────────────────
$router->get( '/account/settings',  'UserDashboardController',  'settings');
$router->post('/account/settings',  'UserDashboardController',  'saveSettings');
$router->post('/account/password',  'UserDashboardController',  'changePassword');
$router->post('/account/delete',    'UserDashboardController',  'deleteAccount');
