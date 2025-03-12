<?php
/**
 * EMu REST API Retrieve Class
 *
 * Handles record retrieval from the EMu REST API using vanilla PHP.
 * 
 * Designed for maximum compatibility across PHP systems (5.2+).
 *
 * @author Joel Ramirez
 * @version 1.0
 * @since March 3, 2025
 */

require_once 'env.php';

class Retrieve {
    protected $_url;
    protected $_port;
    protected $_tenant;
 
    /**
     * Constructor
     * Initializes the class with environment configuration.
     *
     * @return void
     */
    public function __construct() {
        global $EMU_API_BASE_URL, $EMU_API_PORT, $EMU_API_TENANT;
        
        $this->_url = rtrim($EMU_API_BASE_URL, '/');
        $this->_port = $EMU_API_PORT;
        $this->_tenant = $EMU_API_TENANT;
    }

    /**
     * Retrieve a record from the EMu API.
     *
     * @param string $authToken   Authentication token
     * @param string $resource    Resource name
     * @param string $recordIRN   Record IRN
     * @param array  $fieldsToReturn Fields to retrieve
     * @return array|bool         Response data array on success, or false on failure.
     */
    public function getRecord($authToken, $resource, $recordIRN, $fieldsToReturn = array()) {
        // Construct URL with optional fields query string
        $url = $this->_url . ':' . $this->_port . '/' . $this->_tenant . '/' . $resource . '/' . $recordIRN;
        if (!empty($fieldsToReturn)) {
            $fields = implode(",", $fieldsToReturn);
            $url .= "?select=" . urlencode($fields);
        }
        
        // Set HTTP headers
        $headers = array(
            "Authorization: Bearer $authToken",
            "Content-Type: application/json"
        );
        
        // Ensure the cURL extension is available
        if (!function_exists('curl_init')) {
            die("Retrieve Error: cURL extension is not installed or enabled.\n");
        }
        
        // Initialize cURL session
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
         
        // Execute cURL request
        $response = curl_exec($ch);
        
        // Check for cURL errors
        if (curl_errno($ch)) {
            $error = 'cURL error: ' . curl_error($ch);
            curl_close($ch);
            die("Retrieve Error: $error");
        }
        
        // Get HTTP status code
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        // Evaluate HTTP response
        if ($httpCode == 200) {
            $data = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                die("Retrieve Error: JSON decoding error.\n");
            }
            return $data;
        } elseif ($httpCode == 401) {
            die("Retrieve Error: Unauthorized access (401). Please check your authentication token.\n");
        } elseif ($httpCode == 404) {
            die("Retrieve Error: Record not found (404). Please check the resource and IRN.\n");
        } else {
            die("Retrieve Error: Failed to retrieve data. HTTP Status Code: $httpCode.\n");
        }
        
        return false;
    }

    /**
     * Helper function to print response in a readable format.
     *
     * @param mixed $data Data to print
     */
    public function printResponse($data) {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
}
?>
