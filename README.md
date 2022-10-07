# strava-api-codeigniter-library - v.1.2
A Codeigniter v3 library class that leverages the Strava API. This library wasn't tested in CI4.

This library is in use on [Rafael Amorim HeatMap (portuguese language)](https://www.rafaelamorim.com.br/heatmap).

See [Strava API documentation](https://developers.strava.com/) to get more information.

## Summary
- [Install instructions](#install-instructions)
- [Functions](#functions)
  - [General](#general)
    - RequestAuthLink
    - getToken
    - getRefreshToken
    - getExpireToken
    - upgradeAceessToken
    - requestAceessToken
    - getTokenData
  - [Activities](#activities)
    - getListOfActivities
    - getActivity
    - getActivityComments
    - getActivityKudoers
    - getActivityLaps
  - [Athlete](#athlete)
    - getAthlete
    - getAthleteZones
    - getAthleteStats
  - [Clubs](#clubs)
    - getListOfClubActivities
    - getClubAdministrators
    - getClub
    - getClubMembers
    - getAthleteClubs
  - [Gear](#gear)
    - getGear
  - [Routes](#routes)
    - getRoute
    - getListOfRoutesFromAthlete
  - [Segment Efforts](#segment-efforts)
    - getListSegmentEfforts
    - getSegmentEffort
  - [Segments](#segments)
    - getSegmentsExplore
    - getSegmentsStarred
    - getSegment
- [ChangeLog](#changelog)
- [Todo](#todo)
- [Contributing](#contributing)

## Install instructions
  - Add 'strava' in application/config/autoload.php. Like this (if you don't have another file to load):
  <pre>$autoload['config'] = array('strava');</pre>
  - Create client_id and client_secret in Strava and put the values generated in application/config/strava.php. Details in this [site](https://developers.strava.com/docs/getting-started/#account)
  - Adjust oauth_access_level in config/strava.php according to your application. Values can be:
    - **read** - Allows access to public segments, public routes, public profile data, public posts, public events, club feeds, and leaderboards. This scope matches the old default scope, except it no longer includes access to activities and certain athlete endpoints mentioned below. 
    - **read_all** - Allows access to view private routes, private segments, and private events. This scope matches the old view_private scope, except that it no longer includes access to private activities.
    - **profile:read_all** - NEW! Allows access to read all profile information even if the user has set their profile visibility to “Followers” or “Only You.”
    - **profile:write** - NEW! Allows access to update the user’s weight and Functional Threshold Power (FTP), and access to star or unstar segments on their behalf.
    - **activity:read** - NEW! Allows access to read the user’s activity data for activities that are visible to “Everyone” and “Followers.”
    - **activity:read_all** - NEW! Allows the same access as activity:read, plus access to read the athlete’s activities that are visible to “Only You.”
    - **activity:write** - NEW! Allows access to create manual activities and uploads, and access to edit any activities that are visible to the app (based activity read access level).
    - Source: [Strava OAuth options (2019-02-18)](https://developers.strava.com/docs/oauth-updates/)
Have fun!

------------

## Functions

### General

#### RequestAuthLink($msg = "Your text here", $openPopUp = false, $retFunction) : string
Show link to request strava authentication

- Parameters
  - $msg : string => Message or html code to show link
  - $openPopUp: boolean => return javascript popup? Default is false
  - $retFunction : string => JS function to call on return
- Return values:  string

#### getToken() : string
A function for requesting an oath token from the strava api

- Parameters
  - none
- Return values:  string


#### getRefreshToken($token) : string
Get refresh token key

- Parameters
  - $token : string => Connection token
- Return values:  string

#### getExpireToken($token) : int
Get the expires_at field from token request

- Parameters
  - $token : string => Connection token
- Return values:  int

#### upgradeAceessToken($token)
Perform a upgrade on token OAuth to strava new rule. After 10-15-2019 this function will be removed.
- Parameters
  - $token : string => Forever_Access_Token_For_User
- Return values:  boolean

#### requestAceessToken($refresh_token, $returnField = 'access_token')
Get a refresh access token
      
- Parameters
  - $refresh_token : string => Refresh token
  - $returnField : string => Field will be returned
- Return values:  string or false
     
#### getTokenData($grant_type = 'authorization_code', $refresh_token = null)
Get token data

- Parameters
  - $grant_type : string => Options are 'authorization_code' or 'refresh_token'
  - $refresh_token : string => In case grant_type='refresh_token', refresh_token is needed
- Return values:  array or false, on error

------------

### Activities

#### getListOfActivities(string $token[, int $page = 1 ][, int $per_page = 30 ], int $before, int $after) : string
Returns the activities of an athlete for a specific identifier. Requires activity:read. Only Me activities will be filtered out unless requested by a token with activity:read_all.

- Parameters
 - $token : string
 - $page : int => Page number. Defaults to 1.
 - $per_page : int => Number of items per page. Defaults to 30.
 - $before : int => An epoch timestamp to use for filtering activities that have taken place before a certain time.
 - $after : int => An epoch timestamp to use for filtering activities that have taken place after a certain time.
- Return values: string

#### getActivity(string $token, string $idActivity[, bool $all_efforts = true ]) : string
Returns the given activity that is owned by the authenticated athlete. Requires activity:read for Everyone and Followers activities. Requires activity:read_all for Only Me activities. Parameters token and idActivity must belong to same athlete

- Parameters
 - $token : string => Token from athlete
 - $idActivity : string => The identifier of the activity.
 - $all_efforts : bool => To include all segments efforts. Default is true
- Return values: string

#### getActivityComments(string $token, string $idActivity[, int $per_page = 200 ][, int $page = 1 ]) : string
Returns the comments on the given activity. Requires activity:read for Everyone and Followers activities. Requires activity:read_all for Only Me activities. Parameters token and idActivity must belong to same athlete.

- Parameters
 - $token : string => Token from athlete
 - $idActivity : string => The identifier of the activity.
 - $per_page : int = 200 => Number of items per page. Defaults to 200.
 - $page : int = 1 => Page number. Defaults to 1.
- Return values: string

#### getActivityKudoers(string $token, string $idActivity[, int $per_page = 200 ][, int $page = 1 ]) : string
Returns the athletes who kudoed an activity identified by an identifier. Requires activity:read for Everyone and Followers activities. Requires activity:read_all for Only Me activities. Parameters token and idActivity must belong to same athlete

- Parameters
 - $token : string => Token from athlete
 - $idActivity : string => The identifier of the activity.
 - $per_page : int => Number of items per page. Defaults to 200.
 - $page : int => Page number. Defaults to 1.
- Return values: string

#### getActivityLaps(string $token, string $idActivity) : string
Returns the laps of an activity identified by an identifier. Requires activity:read for Everyone and Followers activities. Requires activity:read_all for Only Me activities.

- Parameters
 - $token : string => Token from athlete
 - $idActivity : string => The identifier of the activity.
- Return values: string

------------

### Athlete

#### getAthlete(string $token) : string
Returns the currently authenticated athlete.

- Parameters
 - $token : string
Return values: string

#### getAthleteZones(string $token) : string
Returns the the authenticated athlete's heart rate and power zones. Requires profile:read_all.

- Parameters
 - $token : string
- Return values: string

#### getAthleteStats(string $token, string $id[, int $page = 1 ][, int $per_page = 30 ]) : string
Returns the activity stats of an athlete. Only includes data from activities set to Everyone visibilty.

- Parameters
 - $token : string => Token from athlete
 - $id : string => The identifier of the athlete. Must match the authenticated athlete.
 - $page : int => Page number. Defaults to 1.
 - $per_page : int => Number of items per page. Defaults to 30.
- Return values: string

------------

### Clubs

#### getListOfClubActivities(string $token, string $id[, int $page = 1 ][, int $per_page = 30 ]) : string
Retrieve recent activities from members of a specific club. The authenticated athlete must belong to the requested club in order to hit this endpoint. Pagination is supported. Enhanced Privacy Mode is respected for all activities.

- Parameters
 - $token : string => Token from athlete
 - $id : string => The identifier of the club.
 - $page : int => Page number. Defaults to 1.
 - $per_page : int => Number of items per page. Defaults to 30.
- Return values: string

#### getClubAdministrators(string $token, string $id[, int $page = 1 ][, int $per_page = 30 ]) : string
Returns a list of the administrators of a given club.

- Parameters
 - $token : string => Token from athlete
 - $id : string => The identifier of the club.
 - $page : int => Page number. Defaults to 1.
 - $per_page : int => Number of items per page. Defaults to 30.
- Return values: string

#### getClub(string $token, string $id) : string
Returns a given club using its identifier.

- Parameters
 - $token : string => Token from athlete
 - $id : string => The identifier of the club.
- Return values: string

#### getClubMembers(string $token, string $id[, int $page = 1 ][, int $per_page = 30 ]) : string
Returns a list of the athletes who are members of a given club.

- Parameters
 - $token : string => Token from athlete
 - $id : string => The identifier of the club.
 - $page : int => Page number. Defaults to 1.
 - $per_page : int => Number of items per page. Defaults to 30.
- Return values: string

#### getAthleteClubs(string $token[, int $page = 1 ][, int $per_page = 30 ]) : string
Returns a list of the clubs whose membership includes the authenticated athlete.

- Parameters
 - $token : string => Token from athlete
 - $page : int => Page number. Defaults to 1.
 - $per_page : int => Number of items per page. Defaults to 30.
- Return values: string

------------

### Gear

#### getGear(string $token, string $id) : string
Returns an equipment using its identifier.

- Parameters
 - $token : string => Token from athlete
 - $id : string => The identifier of the gear.
- Return values: string

------------

### Routes

#### getRoute(string $token, string $id) : string
Returns a route using its identifier. Requires read_all scope for private routes.

- Parameters
 - $token : string => Token from athlete
 - $id : string => The identifier of the route.
- Return values: string

#### getListOfRoutesFromAthlete(string $token, int $id[, int $page = 1 ][, int $per_page = 30 ]) : string
Returns a list of the routes created by the authenticated athlete. Private routes are filtered out unless requested by a token with read_all scope.

- Parameters
 - $token : string
 - $id : int => Athlete id
 - $page : int => Page number. Defaults to 1.
 - $per_page : int => Number of items per page. Defaults to 30.
- Return values: string

------------

### Segment Efforts

#### getListSegmentEfforts(mixed $token, string $segment_id, string $start_date, string $end_date[, int $per_page = 30 ]) : string

Returns a set of the authenticated athlete's segment efforts for a given segment. Requires subscription.

- Parameters
 - $token : mixed
 - $segment_id : string => The identifier of the segment.
 - $start_date : string => ISO 8601 (YYYY-MM-DD) formatted date time.
 - $end_date : string => ISO 8601 (YYYY-MM-DD) formatted date time.
 - $per_page : int => Number of items per page. Defaults to 30.
- Return values: string

#### getSegmentEffort(string $token, string $segment_id) : string
Returns a segment effort from an activity that is owned by the authenticated athlete. Requires subscription.

- Parameters
 - $token : string
 - $segment_id : string => The identifier of the segment effort.
- Return values: string

------------

### Segments functions

#### getSegmentsExplore(string $token, string $bounds, string $activity_type, string $min_cat, string $max_cat) : string
Returns the top 10 segments matching a specified query.

- Parameters
 - $token : string
 - $bounds : string => The latitude and longitude for two points describing a rectangular boundary for the search: [southwest corner latitutde, southwest corner longitude, northeast corner latitude, northeast corner longitude]
 - $activity_type : string => Desired activity type. May take one of the following values: running, riding
 - $min_cat : string => The minimum climbing category.
 - $max_cat : string => The maximum climbing category.
- Return values: string

#### getSegmentsStarred(mixed $token[, int $page = 1 ][, int $per_page = 30 ]) : string
List of the authenticated athlete's starred segments. Private segments are filtered out unless requested by a token with read_all scope.

- Parameters
 - $token : mixed
 - $page : int => Page number. Defaults to 1.
 - $per_page : int => Number of items per page. Defaults to 30.
- Return values: string

#### getSegment(string $token, int $id) : string
Returns the specified segment. read_all scope required in order to retrieve athlete-specific segment information, or to retrieve private segments.

- Parameters
 - $token : string
 - $id : int => The identifier of the segment.
- Return values: string

------------

## ChangeLog

- 1.0.0 
  - First version 
- 1.1
  - OAuth Strava updates, described in https://developers.strava.com/docs/oauth-updates/
  - Some documentation
- 1.2
  - Added functions to Segments, SegmentEfforts, Clubs, Routes
  - Added full documentation 
  - Added CONTRIBUTORS.md in repo
  - Added changelog into README.md


------------

## Todo

### Activities
 - Create an Activity
 - Get Activity Zones
 - Update Activity

### Athletes
 - Update Athlete

### Routes
 - Export Route GPX
 - Export Route TCX

### Segments
 - Star Segment

### Streams
 - Get Activity Streams
 - Get Route Streams
 - Get Segment Effort Streams
 - Get Segment Streams

### Uploads
 - Upload Activity
 - Get Upload

------------

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.