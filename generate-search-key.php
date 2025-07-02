<?php
/**
 * Simple script to generate a search-only API key for Typesense
 * Run this script from the command line: php generate-search-key.php
 */

// Load environment variables from .env file
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// Get Typesense configuration
$typesenseHost = getenv('TYPESENSE_HOST') ?: 'localhost';
$typesensePort = getenv('TYPESENSE_PORT') ?: '8108';
$typesenseProtocol = getenv('TYPESENSE_PROTOCOL') ?: 'http';
$typesenseApiKey = getenv('TYPESENSE_API_KEY');

if (!$typesenseApiKey) {
    echo "Error: TYPESENSE_API_KEY not found in .env file\n";
    exit(1);
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
    'actions' => $searchOnlyActions,
    'collections' => $collections,
    'value' => $keyValue
]);

// Send the request to Typesense
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-TYPESENSE-API-KEY: ' . $typesenseApiKey
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Process the response
if ($httpCode >= 200 && $httpCode < 300) {
    echo "Search-only API key generated successfully\n";
    echo "Add this to your .env file:\n";
    echo "TYPESENSE_SEARCH_ONLY_KEY={$keyValue}\n";
    
    // Ask if user wants to update .env file
    echo "\nWould you like to update your .env file automatically? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    if (trim($line) === 'y') {
        $envContent = file_get_contents($envFile);
        
        // Check if the key already exists
        if (strpos($envContent, 'TYPESENSE_SEARCH_ONLY_KEY=') !== false) {
            // Replace existing key
            $envContent = preg_replace(
                '/TYPESENSE_SEARCH_ONLY_KEY=.*/',
                'TYPESENSE_SEARCH_ONLY_KEY=' . $keyValue,
                $envContent
            );
        } else {
            // Add new key
            $envContent .= "\nTYPESENSE_SEARCH_ONLY_KEY=" . $keyValue;
        }
        
        file_put_contents($envFile, $envContent);
        echo ".env file updated successfully\n";
    }
} else {
    echo "Failed to generate key. HTTP Code: {$httpCode}\n";
    echo "Response: {$response}\n";
    exit(1);
}