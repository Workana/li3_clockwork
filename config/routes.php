<?php
use lithium\net\http\Router;
use lithium\action\Response;
use Clockwork\Storage\FileStorage;

Router::connect('/__clockwork/{:key}', ['type' => 'json', 'continue' => true], function ($request) {
    $storage = new FileStorage(LITHIUM_APP_PATH . "/resources/tmp/clockwork/");
    $data = $storage->retrieve($request->params['key']);
    $response = new Response([
        'status' => 200,
        'type' => 'json',
        'body' => $data->toJson(),
    ]);
    return $response;
});
