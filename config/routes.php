<?php 

$router->protect('/projects');
$router->protect('/projects/*');
$router->protect('/password-types');
$router->protect('/password-types/*');
$router->protect('/me');

// Define routes
/* Home Page */
$router->get('/', 'HomeController@index');

/* Project Routes */
/* index */
$router->get('/projects', 'ProjectController@index');

/* create form */
$router->get('/projects/create', 'ProjectController@create');

/* store */
$router->post('/projects', 'ProjectController@store');

/* show */
$router->get('/projects/{id}/show', 'ProjectController@show');

/* edit form */
$router->get('/projects/{id}/edit', 'ProjectController@edit');

/* update */
$router->post('/projects/{id}/update', 'ProjectController@update');

/* delete */
$router->post('/projects/{id}/delete', 'ProjectController@destroy');


/* Password types Routes */
/* index */
$router->get('/password-types', 'PasswordTypeController@index');

$router->get('/password-types/{id}/show', 'PasswordTypeController@show');

/* create form */
$router->get('/password-types/create', 'PasswordTypeController@create');

/* store */
$router->post('/password-types', 'PasswordTypeController@store');

/* edit form */
$router->get('/password-types/{id}/edit', 'PasswordTypeController@edit');

/* update */
$router->post('/password-types/{id}/update', 'PasswordTypeController@update');

/* delete */
$router->post('/password-types/{id}/delete', 'PasswordTypeController@destroy');


/* Passwords Routes */

/* create form */
$router->get('/projects/{project_id}/passwords/create', 'PasswordController@create');
/* store */
$router->post('/projects/{id}/passwords', 'PasswordController@store');
/* edit */
$router->get('/projects/{project_id}/passwords/{id}/edit', 'PasswordController@edit');
/* update */
$router->post('/projects/{project_id}/passwords/{id}/update', 'PasswordController@update');
/* delete */
$router->post('/projects/{project_id}/passwords/{id}/delete', 'PasswordController@destroy');

/* Password Type Fields Routes */
/* List fields by type id */
$router->get('/password-types/{password_type_id}/fields',  'PasswordTypeFieldController@index');

/* Get fields as JSON for dynamic updates */
$router->get('/password-types/{password_type_id}/fields/api',  'PasswordTypeFieldController@getFieldsJson');

/* Create field form */
$router->get('/password-types/{password_type_id}/fields/create',  'PasswordTypeFieldController@create');
/* Store field */
$router->post('/password-types/{password_type_id}/fields/store',  'PasswordTypeFieldController@store');

/* Edit field form */
$router->get('/password-types/{password_type_id}/fields/{id}/edit',  'PasswordTypeFieldController@edit');
/* Update field */
$router->post('/password-types/{password_type_id}/fields/{id}/update',  'PasswordTypeFieldController@update');
/* Delete field */
$router->post('/password-types/{password_type_id}/fields/{id}/delete',  'PasswordTypeFieldController@destroy');



// Download file
$router->get('/files/{id}/download', 'FileController@download');
// Delete file
$router->post('/files/{id}/delete', 'FileController@delete');

/* Authentication Routes */
/* Login form */
$router->get('/login', 'AuthController@login');
/* Handle login */
$router->post('/login', 'AuthController@log');
/* Register form */
$router->get('/register', 'AuthController@register');
/* Handle register */
$router->post('/register', 'AuthController@store');
/* Logout */
$router->get('/logout', 'AuthController@logout');
/* User profile */
$router->get('/me', 'AuthController@me');

/* Search routes */
$router->get('/search', 'SearchController@index');
