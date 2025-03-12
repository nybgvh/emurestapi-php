<?php
/**
 * EMu REST API Retrieve Test
 * 
 * Tests the functionality of the Retrieve class using the Auth class for authentication.
 * 
 * @author Joel Ramirez
 * @version 1.0
 * @since March 3, 2025
 */

require_once __DIR__ . '/../src/Auth.php';
require_once __DIR__ . '/../src/Retrieve.php';

echo "Starting Retrieve Test...\n";
echo "----------------------------\n";

// Initialize the Auth class and generate the token
$auth = new Auth();
$authToken = $auth->generateToken()->getToken();

if (!$authToken) {
    die("Error: Failed to generate authentication token.\n");
}

echo "Authentication token generated successfully.\n";

// Initialize the Retrieve class
$retrieve = new Retrieve();

// Define resource, record IRN, and fields to return
$resource = 'ecatalogue';   // Replace with your resource name
$recordIRN = '2';           // Replace with your record IRN
$fieldsToReturn = array('data.irn', 'data.SummaryData', 'data.DarGenus', 'data.DarSpecies'); // Replace with desired fields

// Fetch the record using the generated auth token
$response = $retrieve->getRecord($authToken, $resource, $recordIRN, $fieldsToReturn);

if ($response) {
    echo "Record retrieved successfully. Response:\n";
    $retrieve->printResponse($response);
} else {
    echo "Failed to retrieve record.\n";
}

echo "----------------------------\n";
echo "Retrieve Test Completed.\n";
?>
