<?php
// helpers.php

// Prevent direct access
if (!defined('IN_PROJECT')) {
    die('Unauthorized access');
}

// Prevent function redeclaration
if (!function_exists('getCommentCount')) {
    function getCommentCount($facilityId) {
        // ... existing implementation ...
    }
}

if (!function_exists('escapeHtml')) {
    function escapeHtml($text) {
        // ... existing implementation ...
    }
}