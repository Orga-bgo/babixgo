<?php
/**
 * Google Analytics Tracking Configuration
 * This file provides the GA4 tracking ID for client-side JavaScript
 */

// Google Analytics 4 Measurement ID
// Replace 'G-XXXXXXXXXX' with your actual GA4 Measurement ID
$ga_id = getenv('GA_TRACKING_ID') ?: 'G-XXXXXXXXXX';
?>
<!-- Google Analytics Configuration -->
<div id="trackingConfig" data-ga-id="<?php echo htmlspecialchars($ga_id, ENT_QUOTES, 'UTF-8'); ?>" style="display:none;" aria-hidden="true"></div>
