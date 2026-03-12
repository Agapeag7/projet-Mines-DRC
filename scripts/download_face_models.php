<?php
// CLI helper to fetch face-api.js model weights into the project's `models/`
// directory. Run from workspace root with:
//
//     php scripts/download_face_models.php
//
// It will create `models/` if it doesn't exist and pull the handful of JSON
// manifest files and shards from jsDelivr.  If you prefer another source, edit
// the base URL below.
//
// Note: this script requires PHP with allow_url_fopen enabled, or you can
// adjust it to use curl.

$baseUrl = 'https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/weights';
$files = [
    'tiny_face_detector_model-weights_manifest.json',
    'tiny_face_detector_model-shard1',
    'face_landmark_68_model-weights_manifest.json',
    'face_landmark_68_model-shard1',
    'face_recognition_model-weights_manifest.json',
    'face_recognition_model-shard1',
    // add more filenames here if you need other models (e.g. ssd_mobilenetv1)
];

$modelsDir = __DIR__ . '/../models';
if (!is_dir($modelsDir)) {
    if (!mkdir($modelsDir, 0755, true)) {
        fwrite(STDERR, "Failed to create models directory: $modelsDir\n");
        exit(1);
    }
}

foreach ($files as $file) {
    $url = $baseUrl . '/' . $file;
    $dest = $modelsDir . '/' . $file;

    if (file_exists($dest)) {
        fwrite(STDOUT, "Skipping existing $file\n");
        continue;
    }

    fwrite(STDOUT, "Downloading $file... ");

    // attempt with file_get_contents if allowed
    $data = false;
    if (ini_get('allow_url_fopen')) {
        $data = @file_get_contents($url);
    }

    // fallback to cURL if necessary
    if ($data === false && function_exists('curl_version')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // let user configure as needed
        $data = curl_exec($ch);
        if ($data === false) {
            fwrite(STDERR, "curl error: " . curl_error($ch) . "\n");
        }
        curl_close($ch);
    }

    if ($data === false || $data === null) {
        fwrite(STDERR, "failed\n");
        continue;
    }
    if (file_put_contents($dest, $data) === false) {
        fwrite(STDERR, "could not write to $dest\n");
        continue;
    }
    fwrite(STDOUT, "done\n");
}

fwrite(STDOUT, "All done. Check the models/ directory.\n");
