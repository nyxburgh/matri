<?php
// app/modules/Profile/Routes/profile.routes.php

// ── My Profile ────────────────────────────────────────────────────────────────
$router->get( '/my-profile',                'ProfileController',  'myProfile');

// ── Profile Creation Wizard (step-by-step) ────────────────────────────────────
$router->get( '/profile/create',            'ProfileController',  'createStep1');
$router->post('/profile/basic',             'ProfileController',  'saveBasic');
$router->get( '/profile/create/family',     'ProfileController',  'createStep2');
$router->post('/profile/family',            'ProfileController',  'saveFamily');
$router->get( '/profile/create/horoscope',  'ProfileController',  'createStep3');
$router->post('/profile/horoscope',         'ProfileController',  'saveHoroscope');
$router->get( '/profile/create/photos',     'ProfileController',  'createStep4');
$router->post('/profile/photos/upload',     'ProfileController',  'uploadPhoto');
$router->post('/profile/photos/delete',     'ProfileController',  'deletePhoto');
$router->post('/profile/photos/primary',    'ProfileController',  'setPrimary');

// ── Edit Profile ──────────────────────────────────────────────────────────────
$router->get( '/profile/edit',              'ProfileController',  'edit');
$router->post('/profile/edit/basic',        'ProfileController',  'editBasic');
$router->post('/profile/edit/partner',      'ProfileController',  'savePartnerPref');
$router->post('/profile/privacy',           'ProfileController',  'savePrivacy');

// ── View Profiles (public) ────────────────────────────────────────────────────
$router->get( '/profile/:profile_id',       'ProfileController',  'view');

// ── Shortlist ─────────────────────────────────────────────────────────────────
$router->post('/profile/shortlist',         'ProfileController',  'toggleShortlist');
$router->get( '/shortlist',                 'ProfileController',  'shortlistIndex');

// ── Report ────────────────────────────────────────────────────────────────────
$router->post('/profile/report',            'ProfileController',  'report');
