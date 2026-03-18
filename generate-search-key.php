<?php

/**
 * Simple script to generate a search-only API key for Typesense
 * Run this script from the command line: php generate-search-key.php.
 */

// Load environment variables from .env file
$envFile = __DIR__.'/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
} else {
    echo "Warning: .env file not found. Please create one with the following variables:\n";
    echo "TYPESENSE_HOST=localhost\n";
    echo "TYPESENSE_PORT=8108\n";
    echo "TYPESENSE_PROTOCOL=http\n";
    echo "TYPESENSE_API_KEY=your_typesense_api_key_here\n\n";
    echo "You can copy from .env.example if it exists.\n\n";
}

// Get Typesense configuration
$typesenseHost = getenv('TYPESENSE_HOST') ?: 'localhost';
$typesensePort = getenv('TYPESENSE_PORT') ?: '8108';
$typesenseProtocol = getenv('TYPESENSE_PROTOCOL') ?: 'http';
$typesenseApiKey = getenv('TYPESENSE_API_KEY');

if (!$typesenseApiKey) {
    echo "Error: TYPESENSE_API_KEY not found in .env file\n";
    echo "Please add the following to your .env file:\n";
    echo "TYPESENSE_API_KEY=your_typesense_api_key_here\n\n";
    echo "To get your Typesense API key:\n";
    echo "1. Start your Typesense server\n";
    echo "2. Check your Typesense server logs for the API key\n";
    echo "3. Or use the default key: xyz\n\n";
    exit(1);
}

// Test connection to Typesense server
echo "Testing connection to Typesense server at {$typesenseProtocol}://{$typesenseHost}:{$typesensePort}...\n";

$testUrl = "{$typesenseProtocol}://{$typesenseHost}:{$typesensePort}/health";
$ch = curl_init($testUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
$testResponse = curl_exec($ch);
$testHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($testHttpCode !== 200) {
    echo "Warning: Could not connect to Typesense server. HTTP Code: {$testHttpCode}\n";
    echo "Please make sure your Typesense server is running.\n";
    echo "You can start it with: ./typesense-server --data-dir=./data --api-key=xyz --enable-cors\n\n";
}

// Generate a random key value
$keyValue = bin2hex(random_bytes(16));

// Define search-only permissions
$searchOnlyActions = ['documents:search'];
$collections = ['*']; // All collections

// Create the API request
$url = "{$typesenseProtocol}://{$typesenseHost}:{$typesensePort}/keys";
$data = json_encode([
    'description' => 'Search-only API key',
    'actions'     => $searchOnlyActions,
    'collections' => $collections,
    'value'       => $keyValue,
]);

echo "Generating search-only API key...\n";

// Send the request to Typesense
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-TYPESENSE-API-KEY: '.$typesenseApiKey,
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Process the response
if ($httpCode >= 200 && $httpCode < 300) {
    echo "✅ Search-only API key generated successfully\n";
    echo "Add this to your .env file:\n";
    echo "TYPESENSE_SEARCH_ONLY_KEY={$keyValue}\n";

    // Ask if user wants to update .env file
    if (file_exists($envFile)) {
        echo "\nWould you like to update your .env file automatically? (y/n): ";
        $handle = fopen('php://stdin', 'r');
        $line = fgets($handle);
        if (trim($line) === 'y') {
            $envContent = file_get_contents($envFile);

            // Check if the key already exists
            if (strpos($envContent, 'TYPESENSE_SEARCH_ONLY_KEY=') !== false) {
                // Replace existing key
                $envContent = preg_replace(
                    '/TYPESENSE_SEARCH_ONLY_KEY=.*/',
                    'TYPESENSE_SEARCH_ONLY_KEY='.$keyValue,
                    $envContent
                );
            } else {
                // Add new key
                $envContent .= "\nTYPESENSE_SEARCH_ONLY_KEY=".$keyValue;
            }

            file_put_contents($envFile, $envContent);
            echo "✅ .env file updated successfully\n";
        }
    } else {
        echo "\nNo .env file found. Please create one and add:\n";
        echo "TYPESENSE_SEARCH_ONLY_KEY={$keyValue}\n";
    }
} else {
    echo "❌ Failed to generate key. HTTP Code: {$httpCode}\n";
    if ($curlError) {
        echo "cURL Error: {$curlError}\n";
    }
    echo "Response: {$response}\n";

    if ($httpCode === 0) {
        echo "\nPossible issues:\n";
        echo "1. Typesense server is not running\n";
        echo "2. Wrong host/port configuration\n";
        echo "3. Network connectivity issues\n";
        echo "\nTo start Typesense server:\n";
        echo "./typesense-server --data-dir=./data --api-key=xyz --enable-cors\n";
    } elseif ($httpCode === 401) {
        echo "\nAuthentication failed. Please check your TYPESENSE_API_KEY.\n";
    } elseif ($httpCode === 403) {
        echo "\nPermission denied. Please check your API key permissions.\n";
    }

    exit(1);
}
