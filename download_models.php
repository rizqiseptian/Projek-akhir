<?php

$modelsUrl = 'https://raw.githubusercontent.com/vladmandic/face-api/master/model/';
$models = [
    'ssd_mobilenetv1_model-weights_manifest.json',
    'ssd_mobilenetv1_model-shard1',
    'ssd_mobilenetv1_model-shard2',
    'face_landmark_68_model-weights_manifest.json',
    'face_landmark_68_model-shard1',
    'face_recognition_model-weights_manifest.json',
    'face_recognition_model-shard1',
    'face_recognition_model-shard2'
];

$publicModelsDir = __DIR__ . '/public/models';
if (!is_dir($publicModelsDir)) {
    mkdir($publicModelsDir, 0755, true);
}

foreach ($models as $model) {
    echo "Downloading $model...\n";
    $content = @file_get_contents("$modelsUrl/$model");
    if ($content !== false) {
        file_put_contents("$publicModelsDir/$model", $content);
    } else {
        echo "Failed to download $model\n";
    }
}

$jsUrl = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.12/dist/face-api.min.js';
$publicJsDir = __DIR__ . '/public/js';
if (!is_dir($publicJsDir)) {
    mkdir($publicJsDir, 0755, true);
}

echo "Downloading face-api.min.js...\n";
file_put_contents("$publicJsDir/face-api.min.js", file_get_contents($jsUrl));

echo "Done.\n";
