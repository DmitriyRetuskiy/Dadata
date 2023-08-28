<?php

use Laravel\Lumen\Routing\Router;

/**
 * @var Router $router
 **/


$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('/upload', 'UploadController@uploadAttachment');
    //$router->post('/upload', 'UploadControllerOld@uploadFile');
    $router->get('/upload/{key}', 'UploadController@getAttachment');
    $router->get('/upload', 'UploadController@getAllAttachments');
    //$router->delete('/upload/{key}/delete', 'UploadController@deleteAttachment');
    $router->delete('/upload', 'UploadController@deleteAttachments');
    $router->post('/mask', 'MaskController@getMaskDate');
    $router->get('/mask-result', 'MaskController@getMaskResult');
    $router->get('/sql', 'MaskController@getSQLDate');
});

$router->get('/dadata', 'TakeFormDataController@formData');
$router->post('/dadata', 'TakeFormDataController@formDataPost');


