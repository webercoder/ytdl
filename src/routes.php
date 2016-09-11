<?php
// Routes

$app->get('/', function($request, $response, $args) {
    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->post('/', function($request, $response, $args) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $data = $request->getParsedBody();
    $url = filter_var($data['url'], FILTER_SANITIZE_STRING);
    $this->logger->info("{$ip} is requesting {$url}");

    if (preg_match('/v=([A-Za-z0-9\-\_]+)/', $url, $matches) && strlen($matches[1]) > 0) {
        $filename = $matches[1];
        $downloadDir = dirname(__FILE__).'/../public/download';
        $outputFormat = "{$downloadDir}/{$filename}.%(ext)s";
        $cmd = "youtube-dl -o '{$outputFormat}' --cache-dir '{$downloadDir}' --extract-audio --audio-format mp3 'https://www.youtube.com/watch?v={$filename}' 2>&1";
        $this->logger->info("Running command '{$cmd}'");
        $output = `$cmd`;

        $this->logger->info("Result of command: {$output}");

        if (file_exists("{$downloadDir}/{$filename}.mp3")) {
            return $this->renderer->render($response, 'download.phtml', array(
                'file' => "$filename.mp3"
            ));
        } else {
            return $this->renderer->render($response, 'error.phtml', array(
                'message' => "For some reason the audio track couldn't be ripped. The output of youtube-dl was: <pre>{$output}</pre>"
            ));
        }
    } else {
        return $this->renderer->render($response, 'error.phtml', array(
            'message' => "I don't know how to work with the URL, {$url}. Please provide a URL of the format, https://www.youtube.com/watch?v=id-here."
        ));
    }
});
