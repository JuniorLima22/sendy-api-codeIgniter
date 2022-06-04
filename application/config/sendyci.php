<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// REQUIRED CONFIG
$config['sendy_api_key']    = getenv('SENDY_API_KEY');  // SENDY API KEY
$config['sendy_host']       = getenv('SENDY_HOST'); // SENDY INSTALLATION URL (no trailing-slash)

/*
// OPTIONAL CONFIG 
$config['sendy_list_id']    = ''; // LIST ID
$config['sendy_timeout']    = 120; // cURL Timeout
*/

/* End of file sendyci.php */
/* Location: ./application/config/sendyci.php */