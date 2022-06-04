<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sendyci {
    private $key;
    private $host;
    private $timeout;
    
    private $sendy_list_id;
    
    public function __construct() {
        $this->_check_compatibility();
        
        $CI =& get_instance();
        $CI->load->config('sendyci');

        if ($CI->config->item('sendy_api_key')!==FALSE) $this->key = $CI->config->item('sendy_api_key'); else throw new Exception('Undefined variable SENDY_API_KEY');
        if (!empty($CI->config->item('sendy_api_key'))) $this->key = $CI->config->item('sendy_api_key'); else throw new Exception('Empty variable SENDY_API_KEY');

        if ($CI->config->item('sendy_host')!==FALSE) $this->host = $CI->config->item('sendy_host'); else throw new Exception('Undefined variable SENDY_HOST');
        if (!empty($CI->config->item('sendy_host'))) $this->host = $CI->config->item('sendy_host'); else throw new Exception('Empty variable SENDY_HOST');
        
        $this->timeout = ($CI->config->item('sendy_sendy_list_id')!==FALSE) ? $CI->config->item('sendy_sendy_list_id') : '';
        $this->timeout = ($CI->config->item('sendy_timeout')!==FALSE) ? $CI->config->item('sendy_timeout') : 120;
    }
    
    private function _check_compatibility() {
        if (!extension_loaded('curl')) throw new Exception('There are missing dependant extensions - please ensure cURL module are installed');
    }
    
    private function _curl_execute($type, $values) {
        //error checking
        if (empty($type)) throw new Exception("Required config parameter [type] is not set or empty", 1);
        if (empty($values)) throw new Exception("Required config parameter [values] is not set or empty", 1);

        //Global options for return
        $return_options = array(
            'list' => $this->sendy_list_id,
            'boolean' => 'true'
        );

        //Merge the passed in values with the options for return
        $content = array_merge($values, $return_options);

        //build a query using the $content
        $postdata = http_build_query($content);

        $ch = curl_init($this->host .'/'. $type);

        // Settings to disable SSL verification for testing (leave commented for production use)
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        $result = curl_exec($ch);
        curl_close($ch);
        unset($ch);
        
        return $result;
    }
    
    public function set_list_id($id)
    {
        $this->sendy_list_id = $id;
    }
    
    public function get_list_id()
    {
        return $this->sendy_list_id;
    }

    /**
     * This method returns the full list of brands (ids and names) that exists in the Sendy installation.
     *
     * @return Array
     **/
    public function get_brands()
    {
        $type = '/api/brands/get-brands.php';

        //Send request for brands
        $result = $this->_curl_execute($type, array(
            'api_key' => $this->key,
        ));

        $obj = json_decode($result, true);
        
        //Success
        if (json_last_error() == 0) {
            return array(
                'status' => 'success',
                'data' => $obj
            );
        }

        //Error
        return array(
            'status' => 'error',
            'message' => $result
        );
    }

    /**
     * This method returns all lists (ids and names) from a particular brand.
     *
     * @return Array
     **/
    public function get_all_lists_brand($brand_id = null)
    {
        $type = '/api/lists/get-lists.php';

        if (empty($brand_id)) {
            return array(
                'status' => 'error',
                'message' => "method [get_lists] requires parameter [brand_id] to be set."
            );
        }

        //Send request
        $result = $this->_curl_execute($type, array(
            'api_key' => $this->key,
            'brand_id' => $brand_id,
        ));

        $obj = json_decode($result, true);
        
        //Success
        if (json_last_error() == 0) {
            return array(
                'status' => 'success',
                'data' => $obj
            );
        }

        //Error
        return array(
            'status' => 'error',
            'message' => $result
        );
    }
}
