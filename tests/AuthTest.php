<?php
/**
 * Unit Test File for Auth Class
 * 
 * Provides comprehensive unit testing for the EMu REST API Authentication class.
 * Compatible with PHP 5.0+ (requires 5.2+ for JSON), using vanilla PHP with no external frameworks.
 * Tests various success and failure scenarios for broad usability.
 * 
 * @author Joel Ramirez
 * @version 1.0
 * @since March 10, 2025
 */

require_once __DIR__ . '/../src/Auth.php';

/**
 * Simple Test Class for Auth
 * 
 * Implements assertion methods and test runner to validate Auth class behavior.
 */
class AuthTest {
    private $passed = 0;
    private $failed = 0;

    /**
     * Asserts that two values are equal
     * 
     * @param mixed  $expected Expected value
     * @param mixed  $actual   Actual value
     * @param string $message  Test description
     * @return void
     */
    public function assertEquals($expected, $actual, $message) {
        if ($expected === $actual) {
            $this->passed++;
            echo "PASS: $message\n";
        } else {
            $this->failed++;
            echo "FAIL: $message - Expected: '$expected', Got: '$actual'\n";
        }
    }

    /**
     * Asserts that a value is not empty
     * 
     * @param mixed  $value   Value to check
     * @param string $message Test description
     * @return void
     */
    public function assertNotEmpty($value, $message) {
        if (!empty($value)) {
            $this->passed++;
            echo "PASS: $message\n";
        } else {
            $this->failed++;
            echo "FAIL: $message - Value is empty\n";
        }
    }

    /**
     * Asserts that a function call terminates with an expected error message.
     * This captures output from die() calls using output buffering.
     * 
     * @param string   $expectedError Expected error message substring
     * @param string   $message       Test description
     * @param callable $testFunction  Function to execute
     * @return void
     */
    public function assertDiesWithError($expectedError, $message, $testFunction) {
        ob_start();
        $testFunction();
        $output = ob_get_clean();

        if (strpos($output, $expectedError) !== false) {
            $this->passed++;
            echo "PASS: $message\n";
        } else {
            $this->failed++;
            echo "FAIL: $message - Expected error containing '$expectedError', Got: '$output'\n";
        }
    }

    /**
     * Runs all test cases.
     * 
     * @return void
     */
    public function run() {
        echo "Starting Auth Class Tests...\n";
        echo "----------------------------\n";

        $this->testTokenGenerationSuccess();
        $this->testMissingConfig();
        $this->testInvalidCredentials();

        echo "----------------------------\n";
        echo "Tests Passed: " . $this->passed . "\n";
        echo "Tests Failed: " . $this->failed . "\n";
        echo "Total Tests: " . ($this->passed + $this->failed) . "\n";
    }

    /**
     * Tests successful token generation and retrieval.
     * 
     * Note: This test assumes that the API endpoint is accessible and will return a token 
     * matching an expected alphanumeric pattern. Adjust the test values or token format check as needed.
     * 
     * @return void
     */
    private function testTokenGenerationSuccess() {
        global $EMU_API_BASE_URL, $EMU_API_PORT, $EMU_API_TENANT, 
               $EMU_API_USER, $EMU_API_PASSWORD;

        // Set valid test values from your example.
        $EMU_API_BASE_URL = 'YOUR_VALID_API_BASE_URL';
        $EMU_API_PORT = 'YOUR_VALID_API_PORT';
        $EMU_API_TENANT = 'YOUR_VALID_API_TENANT';
        $EMU_API_USER = 'YOUR_VALID_API_USER';
        $EMU_API_PASSWORD = 'YOUR_VALID_API_PASSWORD';

        $auth = new Auth();
        $token = $auth->generateToken()->getToken();

        $this->assertNotEmpty($token, "Token should not be empty with valid credentials.\n");
        // Optional: Add a check for the expected token format if known.
        $formatCheck = preg_match('/^[a-zA-Z0-9\-_\.]+$/', $token);
        $this->assertEquals(1, $formatCheck, "Token should match expected alphanumeric format (letters, numbers, -, _, .)\n");
    }

    /**
     * Tests failure when configuration is missing.
     * 
     * @return void
     */
    private function testMissingConfig() {
        global $EMU_API_BASE_URL, $EMU_API_PORT, $EMU_API_TENANT, 
               $EMU_API_USER, $EMU_API_PASSWORD;

        // Test with missing base URL.
        $EMU_API_BASE_URL = '';
        $EMU_API_PORT = 'YOUR_VALID_API_PORT';
        $EMU_API_TENANT = 'YOUR_VALID_API_TENANT';
        $EMU_API_USER = 'YOUR_VALID_API_USER';
        $EMU_API_PASSWORD = 'YOUR_VALID_API_PASSWORD';

        $this->assertDiesWithError(
            "Auth Error: Missing required configuration parameters",
            "Should fail when base URL is missing",
            function() { new Auth(); }
        );
    }

    /**
     * Tests failure with invalid credentials.
     * 
     * @return void
     */
    private function testInvalidCredentials() {
        global $EMU_API_BASE_URL, $EMU_API_PORT, $EMU_API_TENANT, 
               $EMU_API_USER, $EMU_API_PASSWORD;

        // Set invalid credentials (assuming these won't work with your API).
        $EMU_API_BASE_URL = 'YOUR_INVALID_API_BASE_URL';
        $EMU_API_PORT = 'YOUR_INVALID_API_PORT';
        $EMU_API_TENANT = 'YOUR_INVALID_API_TENANT';
        $EMU_API_USER = 'YOUR_INVALID_API_USER';
        $EMU_API_PASSWORD = 'YOUR_INVALID_API_PASSWORD';

        $this->assertDiesWithError(
            'Auth Error: Bearer token not found in response headers',
            "Should fail with invalid credentials",
            function() { 
                $auth = new Auth();
                $auth->generateToken()->getToken();
            }
        );
    }
}

// Run the tests
$test = new AuthTest();
$test->run();
?>
