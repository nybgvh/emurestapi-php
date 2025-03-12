<?php
/**
 * EMu REST API Authentication Class
 * 
 * Handles token generation and retrieval for the EMu REST API using vanilla PHP.
 * @link https://help.emu.axiell.com/emurestapi/latest/04-Resources-Tokens.html
 * 
 * Optimized for PHP 5.0+ compatibility (requires 5.2+ for JSON).
 * Designed for ease of use and reliability across diverse PHP environments.
 * 
 * @author Joel Ramirez
 * @version 1.1
 * @since March 10, 2025
 */

require_once 'env.php';

class Auth {
    protected $token = '';
    protected $baseUrl;
    protected $tenant;
    protected $username;
    protected $password;
    protected $timeout;
    protected $renew;

    /**
     * Constructor
     * Initializes the class with environment configuration from env.php.
     * 
     * @param int  $timeout Request timeout in seconds (default 30)
     * @param bool $renew   Whether to allow token renewal (default true)
     * @return void
     */
    public function __construct($timeout = 30, $renew = true) {
        global $EMU_API_BASE_URL, $EMU_API_PORT, $EMU_API_TENANT, 
               $EMU_API_USER, $EMU_API_PASSWORD;

        if (!$this->_validateConfig()) {
            $this->_dieWithError('Missing required configuration parameters in env.php');
        }

        // Construct base URL with optional port
        $this->baseUrl = rtrim($EMU_API_BASE_URL, '/') . ($EMU_API_PORT ? ':' . $EMU_API_PORT : '');
        $this->tenant = $EMU_API_TENANT;
        $this->username = $EMU_API_USER;
        $this->password = $EMU_API_PASSWORD;
        $this->timeout = $timeout;
        $this->renew = $renew;
    }

    /**
     * Generates an authentication token from the EMu API
     * 
     * @return Auth Returns $this for method chaining
     */
    public function generateToken() {
        // Ensure the cURL extension is available
        if (!function_exists('curl_init')) {
            $this->_dieWithError('cURL extension is not installed or enabled.');
        }
        
        $curl = curl_init();

        // Build API endpoint URL
        $url = $this->baseUrl . '/' . $this->tenant . '/tokens';
        $payload = json_encode(array(
            'username' => $this->username,
            'password' => $this->password,
            // 400 Error when sending timeout and renew properties. ***In review**
            //'timeout' => $this->timeout,
            //'renew' => $this->renew 
        ));

        $headers = [
            'Content-Type' => 'application/json',
        ];

        // Configure cURL options
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);

        // Execute request
        $response = curl_exec($curl);

        // Check for cURL errors
        if (curl_errno($curl)) {
            $error = 'cURL error: ' . curl_error($curl);
            curl_close($curl);
            $this->_dieWithError($error);
        }

        // Check HTTP status code
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($httpCode < 200 || $httpCode >= 300) {
            curl_close($curl);
            $this->_dieWithError("Unexpected HTTP status code: $httpCode\n");
        }

        // Parse response: separate headers from the body
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $headerSize);
        curl_close($curl);

        // Extract and set token
        $this->token = $this->_extractToken($headers);
        if (!$this->token) {
            $this->_dieWithError("Bearer token not found in response headers.\n");
        }

        return $this;
    }

    /**
     * Retrieves the current authentication token
     * 
     * @return string The authentication token
     */
    public function getToken() {
        if (empty($this->token)) {
            $this->_dieWithError("Authentication token not set. Call generateToken() first.\n");
        }
        return $this->token;
    }

    /**
     * Validates required configuration variables
     * 
     * @return bool True if all required config values are set, false otherwise
     */
    private function _validateConfig() {
        global $EMU_API_BASE_URL, $EMU_API_TENANT, $EMU_API_USER, $EMU_API_PASSWORD;

        $required = array(
            'EMU_API_BASE_URL' => $EMU_API_BASE_URL,
            'EMU_API_TENANT'   => $EMU_API_TENANT,
            'EMU_API_USER'     => $EMU_API_USER,
            'EMU_API_PASSWORD' => $EMU_API_PASSWORD
        );

        foreach ($required as $key => $value) {
            if (empty($value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Extracts the Bearer token from response headers
     * 
     * @param string $headers HTTP response headers
     * @return string|bool Token string if found, false otherwise
     */
    private function _extractToken($headers) {
        if (preg_match('/Authorization:\s*Bearer\s+(\S+)/i', $headers, $matches)) {
            return $matches[1];
        }
        return false;
    }

    /**
     * Terminates script with a formatted error message
     * 
     * @param string $message Error message to display
     * @return void
     */
    private function _dieWithError($message) {
        die("Auth Error: $message");
    }
}

// Usage example:
/*
$auth = new Auth();
$token = $auth->generateToken()->getToken();
echo $token;
*/
?>
