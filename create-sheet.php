<?php
require_once 'config.php';
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
create_spreadsheet();

function create_spreadsheet() {
  
    $client = new Google_Client();
    
    $db = new DB();
  
    $arr_token = (array) $db->get_access_token();
    $accessToken = array(
        'access_token' => $arr_token['access_token'],
        'expires_in' => $arr_token['expires_in'],
    );
  
    $client->setAccessToken($accessToken);
    
    $service = new Google_Service_Sheets($client);
    try {
        $spreadsheet = new Google_Service_Sheets_Spreadsheet([
            'properties' => [
                'title' => 'Accurate Weather Report'
            ]
        ]);
          
        $spreadsheet = $service->spreadsheets->create($spreadsheet, [
            'fields' => 'spreadsheetId'
        ]);
       
        echo $spreadsheet->spreadsheetId;
     
        write_to_sheet($spreadsheet->spreadsheetId);
       
    } catch(Exception $e) {
         $e->getMessage();
        if( 401 == $e->getCode() ) {
            $refresh_token = $db->get_refersh_token();
           
            $client = new GuzzleHttp\Client(['base_uri' => 'https://accounts.google.com']);
          
            $response = $client->request('POST', '/o/oauth2/token', [
                'form_params' => [
                    "grant_type" => "refresh_token",
                    "refresh_token" => $refresh_token,
                    "client_id" => GOOGLE_CLIENT_ID,
                    "client_secret" => GOOGLE_CLIENT_SECRET,
                ],
            ]);
  
            $data = (array) json_decode($response->getBody());
            $data['refresh_token'] = $refresh_token;
  
            $db->update_access_token(json_encode($data));
  
            create_spreadsheet();
        } else {
            
            echo $e->getMessage(); //print the error just in case your sheet is not created.
        }
    }
}


  
function write_to_sheet($spreadsheetId = '') {   
  
    $client = new Google_Client();
  
    $db = new DB();
  
    $arr_token = (array) $db->get_access_token();
    $accessToken = array(
        'access_token' => $arr_token['access_token'],
        'expires_in' => $arr_token['expires_in'],
    );
  
    $client->setAccessToken($accessToken);
  
    $service = new Google_Service_Sheets($client);
  
    try {
        $range = 'A1:K1';
        $values = [
            [
                'name',
                'country',
                'region',
                'timezone',
                'rank',
                'latitude',
                'longitude',
                'weather_text',
                'is_day_time',
                'temperature_celsius',
                'temperature_fahrenheit',
            ],
        ];
        $body = new Google_Service_Sheets_ValueRange([
            'values' => $values
        ]);
        $params = [
            'valueInputOption' => 'USER_ENTERED'
        ];
        $result = $service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);
        
        printf("%d cells updated.", $result->getUpdatedCells());
        append_to_sheet($spreadsheetId);
    } catch(Exception $e) {
        if( 401 == $e->getCode() ) {
            $refresh_token = $db->get_refersh_token();
  
            $client = new GuzzleHttp\Client(['base_uri' => 'https://accounts.google.com']);
  
            $response = $client->request('POST', '/o/oauth2/token', [
                'form_params' => [
                    "grant_type" => "refresh_token",
                    "refresh_token" => $refresh_token,
                    "client_id" => GOOGLE_CLIENT_ID,
                    "client_secret" => GOOGLE_CLIENT_SECRET,
                ],
            ]);
  
            $data = (array) json_decode($response->getBody());
            $data['refresh_token'] = $refresh_token;
  
            $db->update_access_token(json_encode($data));
  
            write_to_sheet($spreadsheetId);
        } else {
            echo $e->getMessage(); //print the error just in case your data is not added.
        }
    }
}

  
function append_to_sheet($spreadsheetId = '') {
  
    $client = new Google_Client();
  
    $db = new DB();
  
    $arr_token = (array) $db->get_access_token();
    $accessToken = array(
        'access_token' => $arr_token['access_token'],
        'expires_in' => $arr_token['expires_in'],
    );
  
    $client->setAccessToken($accessToken);
  
    $service = new Google_Service_Sheets($client);
  
    try {
        $range = 'A2:K2';
        $values = getWeatherReportData();
        $body = new Google_Service_Sheets_ValueRange([
            'values' => $values
        ]);
        $params = [
            'valueInputOption' => 'USER_ENTERED'
        ];
        $result = $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);
        printf("%d cells appended.", $result->getUpdates()->getUpdatedCells());
    } catch(Exception $e) {
        if( 401 == $e->getCode() ) {
            $refresh_token = $db->get_refersh_token();
  
            $client = new GuzzleHttp\Client(['base_uri' => 'https://accounts.google.com']);
  
            $response = $client->request('POST', '/o/oauth2/token', [
                'form_params' => [
                    "grant_type" => "refresh_token",
                    "refresh_token" => $refresh_token,
                    "client_id" => GOOGLE_CLIENT_ID,
                    "client_secret" => GOOGLE_CLIENT_SECRET,
                ],
            ]);
  
            $data = (array) json_decode($response->getBody());
            $data['refresh_token'] = $refresh_token;
  
            $db->update_access_token(json_encode($data));
  
            append_to_sheet($spreadsheetId);
        } else {
            echo $e->getMessage(); //print the error just in case your data is not appended.
        }
    }
}
read_sheet('SPREADSHEET_ID');
  
function read_sheet($spreadsheetId = '') {
  
    $client = new Google_Client();
  
    $db = new DB();
  
    $arr_token = (array) $db->get_access_token();
    $accessToken = array(
        'access_token' => $arr_token['access_token'],
        'expires_in' => $arr_token['expires_in'],
    );
  
    $client->setAccessToken($accessToken);
  
    $service = new Google_Service_Sheets($client);
  
    try {
        $range = 'A:B';
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();
  
        if (empty($values)) {
            print "No data found.\n";
        } else {
            foreach ($values as $row) {
                // Print columns A and E, which correspond to indices 0 and 4.
                printf("%s, %s\n", $row[0], $row[1]);
            }
        }
    } catch(Exception $e) {
        if( 401 == $e->getCode() ) {
            $refresh_token = $db->get_refersh_token();
  
            $client = new GuzzleHttp\Client(['base_uri' => 'https://accounts.google.com']);
  
            $response = $client->request('POST', '/o/oauth2/token', [
                'form_params' => [
                    "grant_type" => "refresh_token",
                    "refresh_token" => $refresh_token,
                    "client_id" => GOOGLE_CLIENT_ID,
                    "client_secret" => GOOGLE_CLIENT_SECRET,
                ],
            ]);
  
            $data = (array) json_decode($response->getBody());
            $data['refresh_token'] = $refresh_token;
  
            $db->update_access_token(json_encode($data));
  
            read_sheet($spreadsheetId);
        } else {
            echo $e->getMessage(); //print the error just in case your data is not read.
        }
    }
}

function getWeatherReportData(){
  //fetch top 50 cities data according to locations
$location_data = file_get_contents('https://dataservice.accuweather.com/locations/v1/topcities/50?apikey=N3ZSZpvGGiy5efdods1CoCb05HmYHZRc&details=true');
$location_response_data = json_decode($location_data);

//fetch top 50 cities according to current conditions api 
$condition_data = file_get_contents('https://dataservice.accuweather.com/currentconditions/v1/topcities/50?apikey=N3ZSZpvGGiy5efdods1CoCb05HmYHZRc&details=true');

$location_response_data = json_decode($location_data);
$condition_response_data = json_decode($condition_data);


$finalDataArray =[];
foreach($location_response_data as  $row ){
   
    foreach($condition_response_data as $conditionRow){
        if($row->Key == $conditionRow->Key){
            $values = [
              
                    $name = $row->LocalizedName,
                    $country = $row->Country->LocalizedName,
                    $region = $row->Region->LocalizedName,
                    $timeZone = $row->TimeZone->Name,
                    $latitude = $row->GeoPosition->Latitude,
                    $longitude = $row->GeoPosition->Longitude,
                    $rank = $row->Rank,
                    $weatherText =  $conditionRow->WeatherText,
                    $isDayTime =  $conditionRow->IsDayTime,
                    $temperatureCelsius =  $conditionRow->Temperature->Metric->Value,
                    $temperatureFahrenheit = $conditionRow->Temperature->Imperial->Value
            
            ];
            array_push($finalDataArray,$values);
      }
        
    }
   
}
return $finalDataArray;  
}  