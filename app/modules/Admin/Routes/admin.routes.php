<?php
// app/modules/Admin/Routes/admin.routes.php

// ── Authentication ──────────────────────────────────────────
$router->get( '/admin/login',          'AdminAuthController',         'loginForm');
$router->post('/admin/login',          'AdminAuthController',         'loginPost');
$router->get( '/admin/logout',         'AdminAuthController',         'logout');

// ── Dashboard ───────────────────────────────────────────────
$router->get( '/admin',                'AdminDashboardController',    'index');
$router->get( '/admin/dashboard',      'AdminDashboardController',    'index');

// ── Users ───────────────────────────────────────────────────
$router->get( '/admin/users',          'AdminUserController',         'index');
$router->get( '/admin/users/view/:id', 'AdminUserController',         'detail');
$router->post('/admin/users/block',    'AdminUserController',         'block');
$router->post('/admin/users/delete',   'AdminUserController',         'delete');
$router->post('/admin/users/unblock',  'AdminUserController',         'unblock');

// ── Profiles ────────────────────────────────────────────────
$router->get( '/admin/profiles',            'AdminProfileController',  'index');
$router->get( '/admin/profiles/view/:id',   'AdminProfileController',  'detail');
$router->post('/admin/profiles/approve',    'AdminProfileController',  'approve');
$router->post('/admin/profiles/reject',     'AdminProfileController',  'reject');

// ── Photos ──────────────────────────────────────────────────
$router->get( '/admin/photos',              'AdminPhotoController',    'index');
$router->post('/admin/photos/approve',      'AdminPhotoController',    'approve');
$router->post('/admin/photos/reject',       'AdminPhotoController',    'reject');

// ── Reports ─────────────────────────────────────────────────
$router->get( '/admin/reports',             'AdminReportController',   'index');
$router->get( '/admin/reports/view/:id',    'AdminReportController',   'detail');
$router->post('/admin/reports/action',      'AdminReportController',   'action');

// ── Subscriptions ───────────────────────────────────────────
$router->get( '/admin/subscriptions',       'AdminSubscriptionController', 'index');
$router->get( '/admin/plans',               'AdminSubscriptionController', 'plans');
$router->post('/admin/plans/save',          'AdminSubscriptionController', 'savePlan');

// ── Payments ────────────────────────────────────────────────
$router->get( '/admin/payments',            'AdminPaymentController',  'index');

// ── Activity Logs ───────────────────────────────────────────
$router->get( '/admin/logs',                'AdminLogController',      'index');

// ── Settings ────────────────────────────────────────────────
$router->get( '/admin/settings',            'AdminSettingsController', 'index');
$router->post('/admin/settings/save',       'AdminSettingsController', 'save');

// ── CMS ─────────────────────────────────────────────────────
$router->get( '/admin/cms/stories',         'AdminCmsController',      'stories');
$router->post('/admin/cms/stories/save',    'AdminCmsController',      'saveStory');
$router->post('/admin/cms/stories/delete',  'AdminCmsController',      'deleteStory');
$router->get( '/admin/cms/seo',             'AdminCmsController',      'seoPages');
$router->post('/admin/cms/seo/save',        'AdminCmsController',      'saveSeoPage');
