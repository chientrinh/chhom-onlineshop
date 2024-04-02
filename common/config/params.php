<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/config/params.php $
 * $Id: params.php 1636 2015-10-11 02:51:38Z mori $
 */

/**
 * setup Japanese environments
 */
mb_language('Japanese');
ini_set('mbstring.detect_order', 'auto');
ini_set('mbstring.http_input'  , 'auto');
ini_set('mbstring.http_output' , 'pass');
ini_set('mbstring.internal_encoding', 'utf8');
ini_set('mbstring.script_encoding'  , 'utf8');
ini_set('mbstring.substitute_character', 'none');
mb_regex_encoding('utf8');

/**
 * use 64 bit for hash
 */
ini_set('session.hash_function','sha256');

/**
 * allocate 1GB to process large image files, especially for
 *
 * @common/models/ProductImage.php (worked on 256M)
 */
ini_set("memory_limit","256M"); // total 32GB memory @ arnica.toyouke.com

/**
 * stop process only when 5 min has passed, especially for
 *
 * @backend/controllers/CustomerController.php:customer/index?format=csv
 */
ini_set("max_execution_time", 300);

return [
    'orderEmail'   => 'shop@toyouke.com',
    'supportEmail' => 'member@toyouke.com',
    'adminEmail'   => 'system@toyouke.com',

    'user.passwordResetTokenExpire' => 3600,
];
