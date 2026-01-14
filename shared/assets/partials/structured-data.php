<?php
/**
 * Structured Data Loader with Caching
 * 
 * This partial safely loads JSON structured data files and inlines them into the HTML.
 * Features:
 * - Error handling for missing or unreadable files
 * - Caching to avoid reading files on every request
 * - Uses relative paths via __DIR__ instead of $_SERVER['DOCUMENT_ROOT']
 * - Validates JSON before output
 */

// Simple in-memory cache for the request lifecycle
static $structured_data_cache = [];

/**
 * Load and output structured data JSON
 * 
 * @param string|array $files Single filename or array of filenames (without path)
 * @return void
 */
if (!function_exists('load_structured_data')) {
    function load_structured_data($files) {
        global $structured_data_cache;
        
        // Ensure $files is an array
        if (!is_array($files)) {
            $files = [$files];
        }
        
        // Base directory for structured data files (relative to this partial)
        $base_dir = dirname(__DIR__) . '/assets/js/structured-data/';
        
        foreach ($files as $filename) {
            // Skip empty filenames
            if (empty($filename)) {
                continue;
            }
            
            // Ensure filename has .json extension
            if (substr($filename, -5) !== '.json') {
                $filename .= '.json';
            }
            
            // Check cache first
            $cache_key = $filename;
            if (isset($structured_data_cache[$cache_key])) {
                echo $structured_data_cache[$cache_key];
                continue;
            }
            
            // Build full file path
            $file_path = $base_dir . $filename;
            
            // Security: Prevent path traversal
            $real_path = realpath($file_path);
            $real_base = realpath($base_dir);
            
            if ($real_path === false || strpos($real_path, $real_base) !== 0) {
                // File doesn't exist or is outside the allowed directory
                error_log("Structured data file not found or invalid path: " . $filename);
                continue;
            }
            
            // Try to read the file
            $json_content = @file_get_contents($real_path);
            
            if ($json_content === false) {
                // File couldn't be read
                error_log("Failed to read structured data file: " . $filename);
                continue;
            }
            
            // Validate JSON
            $decoded = json_decode($json_content);
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Invalid JSON
                error_log("Invalid JSON in structured data file: " . $filename . " - " . json_last_error_msg());
                continue;
            }
            
            // Generate output
            $output = '  <script type="application/ld+json">' . "\n";
            $output .= $json_content . "\n";
            $output .= '  </script>' . "\n";
            
            // Cache the output
            $structured_data_cache[$cache_key] = $output;
            
            // Output
            echo $output;
        }
    }
}

// If specific files are passed to this partial via include/require, load them
if (isset($structured_data_files)) {
    load_structured_data($structured_data_files);
}
