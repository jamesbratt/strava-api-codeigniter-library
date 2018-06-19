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
$config['get_athlete_stats_url'] = 'https://www.strava.com/api/v3/athletes/{id}/stats?page={page}&per_page={per_page}';
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
$config['get_route_url'] = '';
$config['list_athlete_routes_url'] = '';

//runningraces
$config['get_running_race_url'] = '';
$config['list_running_races_url'] = '';

//segmentefforts
$config['list_segment_efforts_url'] = '';
$config['get_segment_effort_url'] = '';

//segments
$config['explore_segments_url'] = '';
$config['get_segmentleaderboard_url'] = '';
$config['list_starred_segments_url'] = '';
$config['get_segment_url'] = '';
$config['star_segment_url'] = '';

//streams
$config['get_activity_streams_url'] = '';
$config['get_segment_effort_streams_url'] = '';
$config['get_segment_streams_url'] = '';

//uploads
$config['upload_activity_url'] = '';
$config['get_upload_url'] = '';
