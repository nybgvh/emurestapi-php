<?php
/**
 * EMu REST API Search Test
 * 
 * Tests the functionality of the Search class by performing an advanced search.
 * 
 * @author Joel Ramirez
 * @version 1.0
 * @since March 3, 2025
 */

require_once __DIR__ . '/../src/Auth.php';
require_once __DIR__ . '/../src/Search.php';

echo "Starting Search Test...\n";
echo "----------------------------\n";

// Initialize the Auth class and generate the token
$auth = new Auth();
$authToken = $auth->generateToken()->getToken();

if (!$authToken) {
    die("Error: Failed to generate authentication token.\n");
}

echo "Authentication token generated successfully: $authToken\n";

// Initialize the Search class
$search = new Search();

// Define the form data for advanced search
$formData = [
    'filter' => '{"AND":[{"data.NamLast":{"exact":{"value": "Smith"}}}]}',
    'sort'   => '[{"data.NamFirst":{"order":"asc"}}]',
    'select' => 'data.NamFirst,data.NamLast',
    'limit'  => 10, 
];

// Execute the search query
$searchResult = $search->resource($authToken, 'eparties', $formData);

if ($searchResult) {
    echo "Search result retrieved successfully:\n";
    echo "<pre>";
    print_r($searchResult);
    echo "</pre>\n";
} else {
    echo "No results found or an error occurred during search.\n";
}

echo "----------------------------\n";
echo "Search Test Completed.\n";
?>
