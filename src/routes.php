<?php
// Routes

$app->get('/', function($request, $response, $args) {
    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->post('/url', function($request, $response, $args) {
    $this->logger->info("Requested ".var_export($args));
    return $this->renderer->render($response, 'index.phtml', $args);
});
