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

// the jsDelivr CDN path was returning 404s (package doesn't expose
// weights folder), so grab the files directly from the Github repo's raw
// contents instead.  This may be slower but it works reliably.
$baseUrl = 'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights';
// initially request the three manifest files; shards will be discovered
// dynamically below.
$files = [
    'tiny_face_detector_model-weights_manifest.json',
    'face_landmark_68_model-weights_manifest.json',
    'face_recognition_model-weights_manifest.json',
];


$modelsDir = __DIR__ . '/../models';
if (!is_dir($modelsDir)) {
    if (!mkdir($modelsDir, 0755, true)) {
        fwrite(STDERR, "Failed to create models directory: $modelsDir\n");
        exit(1);
    }
}

$downloadedManifests = [];

foreach ($files as $file) {
    $url = $baseUrl . '/' . $file;
    $dest = $modelsDir . '/' . $file;

    $needDownload = true;
    if (file_exists($dest)) {
        // check for error placeholder text from previous bad download
        $contents = file_get_contents($dest);
        if ($contents !== false && strpos($contents, "Couldn't find the requested file") === false && strpos($contents, '<!DOCTYPE') === false) {
            fwrite(STDOUT, "Skipping existing $file\n");
            $needDownload = false;
        } else {
            fwrite(STDOUT, "Re-downloading $file (previous copy invalid)\n");
            $needDownload = true;
        }
    }
    if (! $needDownload) {
        $downloadedManifests[] = $file;
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
    $downloadedManifests[] = $file;
}

// now parse any downloaded manifests and fetch their referenced shards
foreach ($downloadedManifests as $manifest) {
    $path = $modelsDir . '/' . $manifest;
    $json = @file_get_contents($path);
    if ($json === false) continue;
    $arr = json_decode($json, true);
    if (!is_array($arr)) continue;
    foreach ($arr as $entry) {
        if (isset($entry['paths']) && is_array($entry['paths'])) {
            foreach ($entry['paths'] as $p) {
                // if not already in $files, queue for download
                if (!in_array($p, $files)) {
                    $files[] = $p;
                    // simple immediate download
                    $url = $baseUrl . '/' . $p;
                    $dest = $modelsDir . '/' . $p;
                    if (file_exists($dest)) continue;
                    fwrite(STDOUT, "Downloading shard $p... ");
                    $data = false;
                    if (ini_get('allow_url_fopen')) {
                        $data = @file_get_contents($url);
                    }
                    if ($data === false && function_exists('curl_version')) {
                        $ch = curl_init($url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        $data = curl_exec($ch);
                        curl_close($ch);
                    }
                    if ($data === false) {
                        fwrite(STDERR, "failed\n");
                    } else {
                        file_put_contents($dest, $data);
                        fwrite(STDOUT, "done\n");
                    }
                }
            }
        }
    }
}


fwrite(STDOUT, "All done. Check the models/ directory.\n");
