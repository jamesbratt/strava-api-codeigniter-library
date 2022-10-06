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

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * CLass for leveraging the Strava API
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Third Party API's
 * @author		James Bratt
 * @link		
 */
class CI_strava_api
{

    protected $ci;
    protected $client_id;
    protected $client_secret;
    protected $oath_url;

    // --------------------------------------------------------------------

    /**
     * Constructor
     * We will instantiate our config objects here
     * This involves adding your strava app id, client id and api uris to the $config array
     * In application/config/config.php
     */
    public function __construct()
    {
        $this->ci = &get_instance();

        $this->client_id = $this->ci->config->item('client_id');
        $this->client_secret = $this->ci->config->item('client_secret');
        $this->oath_url = $this->ci->config->item('oath_url');
    }

    /**
     * A function for requesting an oath token from the strava api. The token is then used to authenticate further api calls.
     * Pass the strava redirect url from a controller  
     * 
     * @param string $url
     * 
     * @return string
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
        curl_setopt($get_token, CURLOPT_URL, $this->oath_url);
        curl_setopt($get_token, CURLOPT_POST, 1);

        curl_setopt($get_token, CURLOPT_POSTFIELDS, http_build_query(array('client_id' => $this->client_id, 'client_secret' => $this->client_secret, 'code' => $code)));

        curl_setopt($get_token, CURLOPT_RETURNTRANSFER, true);

        $tokenResponse = curl_exec($get_token);

        if (curl_errno($get_token)) {
            echo 'Curl error: ' . curl_error($get_token);
        }

        /**
         * Decode the response to extract the token
         */
        $decodedToken = json_decode($tokenResponse, true);

        $token = $decodedToken['access_token'];

        return $token;

        curl_close($get_token);
    }

    /* #region Activities*/

    /**
     * Returns the activities of an athlete for a specific identifier. Requires activity:read. Only Me activities will be filtered out unless requested by a token with activity:read_all.
     * 
     * @param string $token
     * @param int $per_page Number of items per page. Defaults to 30.
     * @param int $page Page number. Defaults to 1.
     * @param int $before An epoch timestamp to use for filtering activities that have taken place before a certain time.
     * @param int $after An epoch timestamp to use for filtering activities that have taken place after a certain time.
     * 
     * @return string
     */
    public function getListOfActivities($token, $page = 1, $per_page = 30, $before = 0, $after = 0)
    {
        $headers = array('Authorization: Bearer ' . $token, 'per_page=' . $per_page, 'page=' . $page);
        if ($before > 0) {
            array_push($headers, 'before=' . $before);
        }
        if ($after > 0) {
            array_push($headers, 'after=' . $after);
        }
        $curl_handler = curl_init();
        $this->CurlOptions($curl_handler, $headers, $this->ci->config->item('list_athlete_activities'));
        $activityResponse = curl_exec($curl_handler);

        if (curl_errno($curl_handler)) {
            echo 'Curl error: ' . curl_error($curl_handler);
        }

        curl_close($curl_handler);
        return $activityResponse;
    }

    /**
     * Returns the given activity that is owned by the authenticated athlete. Requires activity:read for Everyone and Followers activities. Requires activity:read_all for Only Me activities. 
     * Parameters token and idActivity must belong to same athlete
     * 
     * @param string $token Token from athlete 
     * @param string $idActivity The identifier of the activity.
     * @param bool $all_efforts To include all segments efforts. Default is true
     * @return string
     * 
     */
    public function getActivity($token, $idActivity, $all_efforts = true)
    {
        $headers = array('Authorization: Bearer ' . $token);
        if ($all_efforts) {
            array_push($headers, 'include_all_efforts=true');
        } else {
            array_push($headers, 'include_all_efforts=false');
        }
        $curl_handler = curl_init();

        $url = str_replace("{id}", $idActivity, $this->ci->config->item('get_activities_url'));
        $this->CurlOptions($curl_handler, $headers, $url);
        $activityResponse = curl_exec($curl_handler);

        if (curl_errno($curl_handler)) {
            echo 'Curl error: ' . curl_error($curl_handler);
        }

        curl_close($curl_handler);

        return $activityResponse;
    }

    /**
     * Returns the comments on the given activity. Requires activity:read for Everyone and Followers activities. Requires activity:read_all for Only Me activities. 
     * Parameters token and idActivity must belong to same athlete.
     * 
     * @param string $token Token from athlete
     * @param string $idActivity The identifier of the activity.
     * @param int $per_page Number of items per page. Defaults to 200.
     * @param int $page Page number. Defaults to 1.
     * @return string
     */
    public function getActivityComments($token, $idActivity, $per_page = 200, $page = 1)
    {
        $headers = array('Authorization: Bearer ' . $token, 'per_page=' . $per_page, 'page=' . $page);
        $curl_handler = curl_init();

        $url = str_replace("{id}", $idActivity, $this->ci->config->item('list_activity_comments_url'));
        $this->CurlOptions($curl_handler, $headers, $url);
        $activityResponse = curl_exec($curl_handler);

        if (curl_errno($curl_handler)) {
            echo 'Curl error: ' . curl_error($curl_handler);
        }

        curl_close($curl_handler);
        return $activityResponse;
    }

    /**
     * Returns the athletes who kudoed an activity identified by an identifier. Requires activity:read for Everyone and Followers activities. Requires activity:read_all for Only Me activities.
     * Parameters token and idActivity must belong to same athlete
     * 
     * @param string $token Token from athlete
     * @param string $idActivity The identifier of the activity.
     * @param int $per_page Number of items per page. Defaults to 200.
     * @param int $page Page number. Defaults to 1.
     * @return string
     */
    public function getActivityKudoers($token, $idActivity, $per_page = 200, $page = 1)
    {
        $headers = array('Authorization: Bearer ' . $token);
        $curl_handler = curl_init();

        $url = str_replace("{id}", $idActivity, $this->ci->config->item('list_activity_kudoers_url'));
        $url = str_replace("{page}", $page, $url);
        $url = str_replace("{per_page}", $per_page, $url);
        $this->CurlOptions($curl_handler, $headers, $url);

        $activityResponse = curl_exec($curl_handler);

        if (curl_errno($curl_handler)) {
            echo 'Curl error: ' . curl_error($curl_handler);
        }

        curl_close($curl_handler);
        return $activityResponse;
    }

    /**
     * Returns the laps of an activity identified by an identifier. Requires activity:read for Everyone and Followers activities. Requires activity:read_all for Only Me activities.
     * 
     * @param string $token Token from athlete
     * @param string $idActivity The identifier of the activity.
     * @return string
     */
    public function getActivityLaps($token, $idActivity)
    {
        $headers = array('Authorization: Bearer ' . $token);
        $curl_handler = curl_init();

        $url = str_replace("{id}", $idActivity, $this->ci->config->item('list_activity_laps_url'));
        $this->CurlOptions($curl_handler, $headers, $url);
        $activityResponse = curl_exec($curl_handler);

        if (curl_errno($curl_handler)) {
            echo 'Curl error: ' . curl_error($curl_handler);
        }

        curl_close($curl_handler);
        return $activityResponse;
    }

    /* #endregion */
    /* #region Atlhetes*/
    /**
     * Returns the currently authenticated athlete.
     * 
     * @param string $token
     * @return string
     */
    public function getAthlete($token)
    {
        $headers = array('Authorization: Bearer ' . $token);
        $curl_handler = curl_init();

        $this->CurlOptions($curl_handler, $headers, $this->ci->config->item('get_Authenticated_Athlete_url'));
        $activityResponse = curl_exec($curl_handler);

        if (curl_errno($curl_handler)) {
            echo 'Curl error: ' . curl_error($curl_handler);
        }

        curl_close($curl_handler);
        return $activityResponse;
    }

    /**
     * Returns the the authenticated athlete's heart rate and power zones. Requires profile:read_all.
     * 
     * @param string $token
     * @return string
     */
    public function getAthleteZones($token)
    {
        $headers = array('Authorization: Bearer ' . $token);
        $curl_handler = curl_init();

        $this->CurlOptions($curl_handler, $headers, $this->ci->config->item('get_zones_url'));
        $activityResponse = curl_exec($curl_handler);

        if (curl_errno($curl_handler)) {
            echo 'Curl error: ' . curl_error($curl_handler);
        }

        curl_close($curl_handler);
        return $activityResponse;
    }

    /**
     * Returns the activity stats of an athlete. Only includes data from activities set to Everyone visibilty.
     * 
     * @param string $token Token from athlete
     * @param string $id The identifier of the athlete. Must match the authenticated athlete.
     * @return string
     */
    public function getAthleteStats($token, $id)
    {
        $headers = array('Authorization: Bearer ' . $token);
        $curl_handler = curl_init();
        $url = str_replace("{id}", $id, $this->ci->config->item('get_athlete_stats_url'));

        $this->CurlOptions($curl_handler, $headers, $url);
        $activityResponse = curl_exec($curl_handler);

        if (curl_errno($curl_handler)) {
            echo 'Curl error: ' . curl_error($curl_handler);
        }

        curl_close($curl_handler);
        return $activityResponse;
    }

    /* #endregion */
    /* #region Clubs*/
    /**
     * Retrieve recent activities from members of a specific club. 
     * The authenticated athlete must belong to the requested club in 
     * order to hit this endpoint. Pagination is supported. 
     * Enhanced Privacy Mode is respected for all activities.
     * 
     * @param string $token Token from athlete
     * @param string $id The identifier of the club.
     * @param int $page Page number. Defaults to 1.
     * @param int $per_page Number of items per page. Defaults to 30.
     * @return string
     */
    public function getListOfClubActivities($token, $id, $page = 1, $per_page = 30)
    {
        $headers = array('Authorization: Bearer ' . $token, 'per_page=' . $page, 'per_page=' . $per_page);
        $curl_handler = curl_init();

        $url = str_replace("{id}", $id, $this->ci->config->item('list_club_activities_url'));
        $this->CurlOptions($curl_handler, $headers, $url);

        $activityResponse = curl_exec($curl_handler);

        if (curl_errno($curl_handler)) {
            echo 'Curl error: ' . curl_error($curl_handler);
        }

        curl_close($curl_handler);
        return $activityResponse;
    }

    /**
     * Returns a list of the administrators of a given club.
     * 
     * @param string $token Token from athlete
     * @param string $id The identifier of the club.
     * @param int $page Page number. Defaults to 1.
     * @param int $per_page Number of items per page. Defaults to 30.
     * @return string
     */
    public function getClubAdministrators($token, $id, $page = 1, $per_page = 30)
    {
        $headers = array('Authorization: Bearer ' . $token, 'per_page=' . $page, 'per_page=' . $per_page);
        $curl_handler = curl_init();

        $url = str_replace("{id}", $id, $this->ci->config->item('list_club_administrators_url'));
        $this->CurlOptions($curl_handler, $headers, $url);

        $activityResponse = curl_exec($curl_handler);

        if (curl_errno($curl_handler)) {
            echo 'Curl error: ' . curl_error($curl_handler);
        }

        curl_close($curl_handler);
        return $activityResponse;
    }

    /**
     * Returns a given club using its identifier.
     * 
     * @param string $token Token from athlete
     * @param string $id The identifier of the club.
     * @return string
     */
    public function getClub($token, $id)
    {
        $headers = array('Authorization: Bearer ' . $token);
        $curl_handler = curl_init();

        $url = str_replace("{id}", $id, $this->ci->config->item('get_club_url'));
        $this->CurlOptions($curl_handler, $headers, $url);

        $activityResponse = curl_exec($curl_handler);

        if (curl_errno($curl_handler)) {
            echo 'Curl error: ' . curl_error($curl_handler);
        }

        curl_close($curl_handler);
        return $activityResponse;
    }

    /**
     * Returns a list of the athletes who are members of a given club.
     * 
     * @param string $token Token from athlete
     * @param string $id The identifier of the club.
     * @param int $page Page number. Defaults to 1.
     * @param int $per_page Number of items per page. Defaults to 30.
     * @return string
     */
    public function getClubMembers($token, $id, $page = 1, $per_page = 30)
    {
        $headers = array('Authorization: Bearer ' . $token, 'per_page=' . $page, 'per_page=' . $per_page);
        $curl_handler = curl_init();

        $url = str_replace("{id}", $id, $this->ci->config->item('list_club_members_url'));
        $this->CurlOptions($curl_handler, $headers, $url);

        $activityResponse = curl_exec($curl_handler);

        if (curl_errno($curl_handler)) {
            echo 'Curl error: ' . curl_error($curl_handler);
        }

        curl_close($curl_handler);
        return $activityResponse;
    }

    /**
     * Returns a list of the clubs whose membership includes the authenticated athlete.
     * 
     * @param string $token Token from athlete
     * @param int $page Page number. Defaults to 1.
     * @param int $per_page Number of items per page. Defaults to 30.
     * @return string
     */
    public function getAthleteClubs($token, $page = 1, $per_page = 30)
    {
        $headers = array('Authorization: Bearer ' . $token, 'per_page=' . $page, 'per_page=' . $per_page);
        $curl_handler = curl_init();

        $this->CurlOptions($curl_handler, $headers, $this->ci->config->item('list_athlete_clubs_url'));

        $activityResponse = curl_exec($curl_handler);

        if (curl_errno($curl_handler)) {
            echo 'Curl error: ' . curl_error($curl_handler);
        }

        curl_close($curl_handler);
        return $activityResponse;
    }

    /* #endregion */
    /* #region Gears*/
    /**
     * Returns an equipment using its identifier.
     * 
     * @param string $token Token from athlete
     * @param string $id The identifier of the gear.
     * @return string
     */
    public function getGear($token, $id)
    {
        $headers = array('Authorization: Bearer ' . $token);
        $curl_handler = curl_init();

        $url = str_replace("{id}", $id, $this->ci->config->item('get_equipment_url'));
        curl_setopt($curl_handler, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_handler, CURLOPT_URL, $url);
        curl_setopt($curl_handler, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl_handler, CURLOPT_RETURNTRANSFER, true);

        $activityResponse = curl_exec($curl_handler);

        if (curl_errno($curl_handler)) {
            echo 'Curl error: ' . curl_error($curl_handler);
        }

        curl_close($curl_handler);
        return $activityResponse;
    }

    /* #endregion */
    /* #region Routes */
    /**
     * Returns a route using its identifier. Requires read_all scope for private routes.
     * 
     * @param string $token Token from athlete
     * @param string $id The identifier of the route.
     * 
     * @return string
     */
    public function getRoute($token, $id)
    {
        $headers = array('Authorization: Bearer ' . $token);
        $curl_handler = curl_init();

        $url = str_replace("{id}", $id, $this->ci->config->item('get_route_url'));
        curl_setopt($curl_handler, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_handler, CURLOPT_URL, $url);
        curl_setopt($curl_handler, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl_handler, CURLOPT_RETURNTRANSFER, true);

        $activityResponse = curl_exec($curl_handler);

        if (curl_errno($curl_handler)) {
            echo 'Curl error: ' . curl_error($curl_handler);
        }

        curl_close($curl_handler);
        return $activityResponse;
    }

    /**
     * Returns a list of the routes created by the authenticated athlete. Private routes are filtered out unless requested by a token with read_all scope.
     * 
     * @param string $token
     * @param int $id Athlete id
     * @param int $page Page number. Defaults to 1.
     * @param int $per_page Number of items per page. Defaults to 30.
     * 
     * @return string
     */
    public function getListOfRoutesFromAthlete($token, $id, $page = 1, $per_page = 30)
    {
        $headers = array('Authorization: Bearer ' . $token, 'per_page=' . $per_page, 'page=' . $page);

        $url = str_replace("{id}", $id, $this->ci->config->item('list_athlete_routes_url'));
        $url = str_replace("{page}", $page, $url);
        $url = str_replace("{per_page}", $per_page, $url);

        $curl_handler = curl_init();
        $this->CurlOptions($curl_handler, $headers, $url);
        $activityResponse = curl_exec($curl_handler);

        if (curl_errno($curl_handler)) {
            echo 'Curl error: ' . curl_error($curl_handler);
        }

        curl_close($curl_handler);
        return $activityResponse;
    }
    /* #endregion */
    /* #region Segment Efforts */

    /**
     * Returns a set of the authenticated athlete's segment efforts for a given segment. Requires subscription.
     * 
     * @param mixed $token
     * @param string $segment_id The identifier of the segment.
     * @param string $start_date ISO 8601 (YYYY-MM-DD) formatted date time.
     * @param string $end_date ISO 8601 (YYYY-MM-DD) formatted date time.
     * @param int $per_page Number of items per page. Defaults to 30.
     * 
     * @return string
     */
    function getListSegmentEfforts($token, $segment_id, $start_date, $end_date, $per_page = 30)
    {
        $headers = array('Authorization: Bearer ' . $token);

        $url = str_replace("{segment_id}", $segment_id, $this->ci->config->item('list_segment_efforts_url'));
        $url = str_replace("{start_date_local}", $start_date, $url);
        $url = str_replace("{end_date_local}", $end_date, $url);
        $url = str_replace("{per_page}", $per_page, $url);

        $curl_handler = curl_init();
        $this->CurlOptions($curl_handler, $headers, $url);
        $activityResponse = curl_exec($curl_handler);

        if (curl_errno($curl_handler)) {
            echo 'Curl error: ' . curl_error($curl_handler);
        }

        curl_close($curl_handler);
        return $activityResponse;
    }

    /**
     * Returns a segment effort from an activity that is owned by the authenticated athlete. Requires subscription.
     * 
     * @param string $token
     * @param string $segment_id
     * 
     * @return string
     */
    function getSegmentEffort($token, $segment_id)
    {
        $headers = array('Authorization: Bearer ' . $token);

        $url = str_replace("{segment_id}", $segment_id, $this->ci->config->item('get_segment_effort_url'));

        $curl_handler = curl_init();
        $this->CurlOptions($curl_handler, $headers, $url);
        $activityResponse = curl_exec($curl_handler);

        if (curl_errno($curl_handler)) {
            echo 'Curl error: ' . curl_error($curl_handler);
        }

        curl_close($curl_handler);
        return $activityResponse;
    }
    /* #endregion */
    /* #region Segments */

    /**
     * Returns the top 10 segments matching a specified query.
     * 
     * @param string $token
     * @param string $bounds The latitude and longitude for two points describing a rectangular boundary for the search: [southwest corner latitutde, southwest corner longitude, northeast corner latitude, northeast corner longitude]
     * @param string $activity_type Desired activity type. May take one of the following values: running, riding
     * @param string $min_cat The minimum climbing category.
     * @param string $max_cat The maximum climbing category.
     * 
     * @return string
     */
    function getSegmentsExplore($token, $bounds, $activity_type, $min_cat, $max_cat)
    {
        $headers = array('Authorization: Bearer ' . $token);

        $url = str_replace("{bounds}", $bounds,  $this->ci->config->item('explore_segments_url'));
        $url = str_replace("{activity_type}", $activity_type, $url);
        $url = str_replace("{min_cat}", $min_cat, $url);
        $url = str_replace("{max_cat}", $max_cat, $url);

        $curl_handler = curl_init();
        $this->CurlOptions($curl_handler, $headers, $url);
        $activityResponse = curl_exec($curl_handler);

        if (curl_errno($curl_handler)) {
            echo 'Curl error: ' . curl_error($curl_handler);
        }

        curl_close($curl_handler);
        return $activityResponse;
    }

    /**
     * List of the authenticated athlete's starred segments. Private segments are filtered out unless requested by a token with read_all scope.
     * 
     * @param mixed $token
     * @param int $page Page number. Defaults to 1.
     * @param int $per_page Number of items per page. Defaults to 30.
     * 
     * @return string
     */
    function getSegmentsStarred($token, $page = 1, $per_page = 30){
        $headers = array('Authorization: Bearer ' . $token);

        $url = str_replace("{page}", $page,  $this->ci->config->item('list_starred_segments_url'));
        $url = str_replace("{per_page}", $per_page, $url);

        $curl_handler = curl_init();
        $this->CurlOptions($curl_handler, $headers, $url);
        $activityResponse = curl_exec($curl_handler);

        if (curl_errno($curl_handler)) {
            echo 'Curl error: ' . curl_error($curl_handler);
        }

        curl_close($curl_handler);
        return $activityResponse;
    }

    /**
     * Returns the specified segment. read_all scope required in order to retrieve athlete-specific segment information, or to retrieve private segments.
     * 
     * @param string $token
     * @param int $id
     * 
     * @return string
     */
    function getSegment($token, $id){
        $headers = array('Authorization: Bearer ' . $token);

        $url = str_replace("{id}", $id,  $this->ci->config->item('get_segment_url'));

        $curl_handler = curl_init();
        $this->CurlOptions($curl_handler, $headers, $url);
        $activityResponse = curl_exec($curl_handler);

        if (curl_errno($curl_handler)) {
            echo 'Curl error: ' . curl_error($curl_handler);
        }

        curl_close($curl_handler);
        return $activityResponse;
    }

    /* #endregion */
    /* #region CURL config */
    private function CurlOptions($handler, $headers, $url)
    {
        curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handler, CURLOPT_URL, $url);
        curl_setopt($handler, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
    }

    /* #endregion */
}
