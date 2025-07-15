<?php
/**
 * College Club Website - Core Functions
 * 
 * This file contains all the essential functions used throughout the website.
 */

/**
 * Generate a URL-friendly slug from a string
 * 
 * @param string $string The string to convert to a slug
 * @param string $delimiter Word separator (default: '-')
 * @return string The generated slug
 */
function slugify($string, $delimiter = '-') {
    // Replace non-letter or digits by the delimiter
    $string = preg_replace('~[^\pL\d]+~u', $delimiter, $string);
    
    // Transliterate
    $string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);
    
    // Remove unwanted characters
    $string = preg_replace('~[^-\w]+~', '', $string);
    
    // Trim and convert to lowercase
    $string = trim($string, $delimiter);
    $string = strtolower($string);
    
    // Remove duplicate delimiters
    $string = preg_replace('~-+~', $delimiter, $string);
    
    return $string ?: 'n-a';
}

/**
 * Format a date in a human-readable format
 * 
 * @param string $date The date string to format
 * @param string $format The output format (default: 'F j, Y')
 * @return string Formatted date
 */
function format_date($date, $format = 'F j, Y') {
    if (empty($date) || $date === '0000-00-00 00:00:00') {
        return 'N/A';
    }
    
    try {
        $datetime = new DateTime($date);
        return $datetime->format($format);
    } catch (Exception $e) {
        return 'Invalid date';
    }
}

/**
 * Get the excerpt of a string
 * 
 * @param string $text The text to truncate
 * @param int $length The maximum length of the excerpt
 * @param string $suffix The suffix to append if text is truncated
 * @return string The truncated text
 */
function get_excerpt($text, $length = 160, $suffix = '...') {
    $text = strip_tags($text);
    
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    
    $excerpt = mb_substr($text, 0, $length);
    $lastSpace = mb_strrpos($excerpt, ' ');
    
    if ($lastSpace !== false) {
        $excerpt = mb_substr($excerpt, 0, $lastSpace);
    }
    
    return $excerpt . $suffix;
}

/**
 * Sanitize user input
 * 
 * @param mixed $data The data to sanitize
 * @param string $type The type of data (text, email, int, float, url)
 * @return mixed The sanitized data
 */
function sanitize_input($data, $type = 'text') {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    
    $data = trim($data);
    $data = stripslashes($data);
    
    switch ($type) {
        case 'email':
            $data = filter_var($data, FILTER_SANITIZE_EMAIL);
            break;
            
        case 'int':
            $data = filter_var($data, FILTER_SANITIZE_NUMBER_INT);
            break;
            
        case 'float':
            $data = filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            break;
            
        case 'url':
            $data = filter_var($data, FILTER_SANITIZE_URL);
            break;
            
        case 'html':
            // Allow safe HTML
            $allowed_tags = [
                'a' => ['href' => [], 'title' => []],
                'br' => [],
                'em' => [],
                'strong' => [],
                'p' => [],
                'ul' => [],
                'ol' => [],
                'li' => [],
                'h1' => [],
                'h2' => [],
                'h3' => [],
                'h4' => [],
                'h5' => [],
                'h6' => [],
                'blockquote' => [],
                'code' => [],
                'pre' => [],
                'hr' => []
            ];
            $data = wp_kses($data, $allowed_tags);
            break;
            
        case 'text':
        default:
            $data = filter_var($data, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
            break;
    }
    
    return $data;
}

/**
 * Redirect to a specific URL
 * 
 * @param string $url The URL to redirect to
 * @param int $status_code The HTTP status code (default: 302)
 * @return void
 */
function redirect($url, $status_code = 302) {
    if (!headers_sent()) {
        if (strpos($url, 'http') !== 0) {
            $url = BASE_URL . ltrim($url, '/');
        }
        
        header('Location: ' . $url, true, $status_code);
    } else {
        echo "<script>window.location.href='$url';</script>";
    }
    
    exit();
}

/**
 * Generate a CSRF token
 * 
 * @return string The generated token
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Verify a CSRF token
 * 
 * @param string $token The token to verify
 * @return bool True if the token is valid, false otherwise
 */
function verify_csrf_token($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Display a flash message
 * 
 * @param string $type The type of message (success, error, warning, info)
 * @param string $message The message to display
 * @return void
 */
function set_flash_message($type, $message) {
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }
    
    $_SESSION['flash_messages'][] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Display flash messages and clear them
 * 
 * @return void
 */
function display_flash_messages() {
    if (empty($_SESSION['flash_messages'])) {
        return;
    }
    
    $output = '';
    
    foreach ($_SESSION['flash_messages'] as $message) {
        $output .= sprintf(
            '<div class="alert alert-%s alert-dismissible fade show" role="alert">%s' .
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>',
            htmlspecialchars($message['type']),
            htmlspecialchars($message['message'])
        );
    }
    
    // Clear messages
    unset($_SESSION['flash_messages']);
    
    return $output;
}

/**
 * Check if user is logged in
 * 
 * @return bool True if user is logged in, false otherwise
 */
function is_logged_in() {
    return !empty($_SESSION['user_id']);
}

/**
 * Get the current user's ID
 * 
 * @return int|null The user ID or null if not logged in
 */
function get_current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Check if the current user has a specific role
 * 
 * @param string $role The role to check for
 * @return bool True if the user has the role, false otherwise
 */
function current_user_can($role) {
    if (!is_logged_in()) {
        return false;
    }
    
    // In a real application, you would check the user's roles in the database
    return ($_SESSION['user_role'] ?? '') === $role;
}

/**
 * Get the current URL
 * 
 * @param bool $with_query_string Whether to include the query string
 * @return string The current URL
 */
function get_current_url($with_query_string = true) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    
    if (!$with_query_string) {
        $url = strtok($url, '?');
    }
    
    return $url;
}

/**
 * Generate a random string
 * 
 * @param int $length The length of the string
 * @return string The generated string
 */
function generate_random_string($length = 32) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $random_string = '';
    
    for ($i = 0; $i < $length; $i++) {
        $random_string .= $characters[rand(0, strlen($characters) - 1)];
    }
    
    return $random_string;
}

/**
 * Send an email
 * 
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $message Email body (HTML)
 * @param string $from Sender email address
 * @param string $from_name Sender name
 * @return bool True if the email was sent successfully, false otherwise
 */
function send_email($to, $subject, $message, $from = null, $from_name = null) {
    if ($from === null) {
        $from = SITE_EMAIL;
    }
    
    if ($from_name === null) {
        $from_name = SITE_NAME;
    }
    
    $headers = [];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=utf-8';
    $headers[] = 'From: ' . $from_name . ' <' . $from . '>';
    $headers[] = 'Reply-To: ' . $from_name . ' <' . $from . '>';
    $headers[] = 'X-Mailer: PHP/' . phpversion();
    
    return mail($to, $subject, $message, implode("\r\n", $headers));
}

/**
 * Get the client's IP address
 * 
 * @return string The IP address
 */
function get_client_ip() {
    $ipaddress = '';
    
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    } else if (isset($_SERVER['HTTP_FORWARDED'])) {
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    } else if (isset($_SERVER['REMOTE_ADDR'])) {
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    } else {
        $ipaddress = 'UNKNOWN';
    }
    
    return $ipaddress;
}

/**
 * Format file size in a human-readable format
 * 
 * @param int $bytes File size in bytes
 * @param int $precision Number of decimal places
 * @return string Formatted file size
 */
function format_file_size($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    
    return round($bytes, $precision) . ' ' . $units[$pow];
}

/**
 * Get the file extension from a filename
 * 
 * @param string $filename The filename
 * @return string The file extension (without the dot)
 */
function get_file_extension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Check if a string is a valid JSON
 * 
 * @param string $string The string to check
 * @return bool True if the string is valid JSON, false otherwise
 */
function is_json($string) {
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}

/**
 * Convert a string to a URL-friendly format
 * 
 * @param string $string The string to convert
 * @return string The URL-friendly string
 */
function str_to_url($string) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
}

// Include this file in your config.php or at the top of your pages
// require_once __DIR__ . '/includes/functions.php';
?>
