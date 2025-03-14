<?php
/**
 * EMu REST API Search Class
 * 
 * Handles advanced search functionality for the EMu REST API using vanilla PHP.
 * @link https://help.emu.axiell.com/emurestapi/latest/05-Appendices-Query.html
 * 
 * Designed for maximum compatibility across PHP systems (5.2+).
 * 
 * @author Joel Ramirez
 * @version 1.0
 * @since March 3, 2025
 */

require_once 'env.php';

class Search {
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
     * Performs an advanced search query against the EMu API.
     * 
     * @param string $authToken Authentication token
     * @param string $resource  Resource name to search
     * @param array  $formData  Search filters, sorting, and fields to select
     * @return array|bool       Search results as an associative array on success, or false on failure.
     */
    public function resource($authToken, $resource, $formData) {
        $url = $this->_url . ':' . $this->_port . '/' . $this->_tenant . '/' . $resource;

        // Prepare HTTP headers
        $headers = array(
            "Authorization: Bearer $authToken",
            "Prefer: representation=minimal",
            "X-HTTP-Method-Override: GET",
            "Content-Type: application/x-www-form-urlencoded",
        );
        
        // Prepare POST data
        $postData = http_build_query($formData);
        
        // Ensure the cURL extension is available
        if (!function_exists('curl_init')) {
            die("Search Error: cURL extension is not installed or enabled.\n");
        }
        
        // Initialize cURL session and set options
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
       
        // Execute cURL request
        $response = curl_exec($ch);
        
        // Check for cURL errors
        if (curl_errno($ch)) {
            $error = 'cURL error: ' . curl_error($ch);
            curl_close($ch);
            die("Search Error: $error\n");
        }
        
        // Get HTTP status code and close session
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        // Evaluate HTTP response
        if ($httpCode == 200) {
            $data = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                die("Search Error: JSON decoding error.\n");
            }
            return $data;
        } else {
            die("Search Error: Failed to retrieve data. HTTP Status Code: $httpCode.\n");
        }
        
        return false;
    }
}
?>
