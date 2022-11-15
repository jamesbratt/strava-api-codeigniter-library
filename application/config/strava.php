<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
  |--------------------------------------------------------------------------
  |STRAVA
  |--------------------------------------------------------------------------
  |
  | Strava config parameters
  |
 */

$config['client_id'] = '';
$config['client_secret'] = '';
$config['oauth_access_level'] = 'profile:read_all,activity:read_all';


/* 
 *   BE CAREFUL TO CHANGE VALUES BELOW  
 */

$config['oath_token_url'] = 'https://www.strava.com/oauth/token';
$config['oath_url'] = 'https://www.strava.com/oauth/authorize';

//activities
$config['list_activity_comments_url'] = 'https://www.strava.com/api/v3/activities/{id}/comments';
$config['list_activity_kudoers_url'] = 'https://www.strava.com/api/v3/activities/{id}/kudos?page={page}&per_page={per_page}';
$config['list_activity_laps_url'] = 'https://www.strava.com/api/v3/activities/{id}/laps';
$config['create_activity_url'] = 'https://www.strava.com/api/v3/activities';
$config['list_athlete_activities'] = 'https://www.strava.com/api/v3/athlete/activities';
$config['get_activities_url'] = 'https://www.strava.com/api/v3/activities/{id}';
$config['get_activity_zones'] = 'https://www.strava.com/api/v3/activities/{id}/zones';
$config['update_activity'] = 'https://www.strava.com/api/v3/activities/{id}';

//Athletes
$config['get_Authenticated_Athlete_url'] = 'https://www.strava.com/api/v3/athlete';
$config['get_zones_url'] = 'https://www.strava.com/api/v3/athlete/zones';
$config['get_athlete_stats_url'] = 'https://www.strava.com/api/v3/athletes/{id}/stats';
$config['update_athlete_url'] = 'https://www.strava.com/api/v3/athlete';

//Clubs
$config['list_club_activities_url'] = 'https://www.strava.com/api/v3/clubs/{id}/activities';
$config['list_club_administrators_url'] = 'https://www.strava.com/api/v3/clubs/{id}/admins';
$config['get_club_url'] = 'https://www.strava.com/api/v3/clubs/{id}';
$config['list_club_members_url'] = 'https://www.strava.com/api/v3/clubs/{id}/members';
$config['list_athlete_clubs_url'] = 'https://www.strava.com/api/v3/athlete/clubs';
$config['join_club_url'] = 'https://www.strava.com/api/v3/clubs/{id}/join';
$config['leave_club_url'] = 'https://www.strava.com/api/v3/clubs/{id}/leave';

//gears
$config['get_equipment_url'] = 'https://www.strava.com/api/v3/gear/{id}';

//routes
$config['get_route_url'] = 'https://www.strava.com/api/v3/routes/{id}';
$config['list_athlete_routes_url'] = 'https://www.strava.com/api/v3/athletes/{id}/routes?page={page}&per_page={per_page}';

//segmentefforts
$config['list_segment_efforts_url'] = 'https://www.strava.com/api/v3/segment_efforts?segment_id={segment_id}&start_date_local={start_date_local}&end_date_local={end_date_local}&per_page={per_page}';
$config['get_segment_effort_url'] = 'https://www.strava.com/api/v3/segment_efforts/{id}';

//segments
$config['explore_segments_url'] = 'https://www.strava.com/api/v3/segments/explore?bounds={bounds}&activity_type={activity_type}&min_cat={min_cat}&max_cat={max_cat}';
$config['list_starred_segments_url'] = 'https://www.strava.com/api/v3/segments/starred?page={page}&per_page={per_page}';
$config['get_segment_url'] = 'https://www.strava.com/api/v3/segments/{id}';
$config['star_segment_url'] = '';

//streams
$config['get_activity_streams_url'] = '';
$config['get_segment_effort_streams_url'] = '';
$config['get_segment_streams_url'] = '';

//uploads
$config['upload_activity_url'] = '';
$config['get_upload_url'] = '';
