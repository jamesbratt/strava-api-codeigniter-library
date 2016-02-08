<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2016, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2016, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CLass for leveraging the Strava API
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Third Party API's
 * @author		James Bratt
 * @link		
 */
class CI_strava_api {

	protected $ci;
	
	protected $client_id;
	
	protected $client_secret;
	
	protected $oath_url;
	
	protected $activities_url;

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 * We will instantiate our config objects here
	 * This involves adding your strava app id, client id and api uris to the $config array
	 * In application/config/config.php
	 */
	public function __construct()
	{
		$this->ci =& get_instance();
	 
		$this->client_id = $this->ci->config->item('client_id');
		$this->client_secret = $this->ci->config->item('client_secret');
		$this->oath_url = $this->ci->config->item('oath_url');
		$this->activities_url = $this->ci->config->item('activities_url');
	}
	
	/**
	 * A function for requesting an oath token from the strava api  
	 * The token is then used to authenticate further api calls 
	 * Pass the strava redirect url from a controller  
	*/
	public function getToken($url)	
	{
		/**
		 * Extract auth code from the url  
		*/
		parse_str($url, $params);
		$code = $params['code'];
		
		/**
		 * Use curl post request against the strava oauth endpoint 
		*/
		$get_token = curl_init();
		
		curl_setopt($get_token, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($get_token, CURLOPT_URL,$this->oath_url);
		curl_setopt($get_token, CURLOPT_POST, 1);

		curl_setopt($get_token, CURLOPT_POSTFIELDS, http_build_query(array('client_id' => $this->client_id, 'client_secret' => $this->client_secret, 'code' => $code)));

		curl_setopt($get_token, CURLOPT_RETURNTRANSFER, true);

		$tokenResponse = curl_exec ($get_token);
		
		if(curl_errno($get_token))
		{
			echo 'Curl error: ' . curl_error($get_token);
		}

		/**
		 * Decode the response to extract the token
		*/
		$decodedToken = json_decode($tokenResponse, true);
		
		$token = $decodedToken['access_token'];
		
		return $token;
		
		curl_close ($get_token);

	}
	
	/**
	 * A function to get all strava activities 
	 * A valid token must be passed from the controller  
	*/	
	public function getActivities($token)
	{
		
		/**
		 * Define an array of header values to send in the curl request   
		 * The authorisation header is critical here  
		*/			
		$headers = array('Authorization: Bearer ' . $token, 'per_page=1');
		$get_activities = curl_init();
		
		curl_setopt($get_activities, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($get_activities, CURLOPT_URL, $this->activities_url);
		curl_setopt($get_activities, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($get_activities, CURLOPT_RETURNTRANSFER, true);

		$activityResponse = curl_exec ($get_activities);
		
		if(curl_errno($get_activities))
		{
			echo 'Curl error: ' . curl_error($get_activities);
		}
		
		return $activityResponse;
		
		curl_close ($get_activities);
		
	}	
}