<?php
require_once 'vendor/autoload.php';
require_once 'class-db.php';
define('GOOGLE_CLIENT_ID', '114501719255-icrh7c6c97h83p7583shj8g7mbvuajs1.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
$config = [
    'callback' => 'https://weather.tripscon.com/callback.php',
    'keys'     => [
                    'id' => GOOGLE_CLIENT_ID,
                    'secret' => GOOGLE_CLIENT_SECRET
                ],
    'scope'    => 'https://www.googleapis.com/auth/spreadsheets',
    'authorize_url_parameters' => [
            'approval_prompt' => 'force', // to pass only when you need to acquire a new refresh token.
            'access_type' => 'offline',
            
    ]
];
  
$adapter = new Hybridauth\Provider\Google( $config );
