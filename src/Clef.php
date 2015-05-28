<?php namespace Xavierau\Clef;


use Xavierau\Clef\Exceptions\ClefAuthorizationFailsException;
use Xavierau\Clef\Exceptions\ClefFetchUserInfoFailException;
use Xavierau\Clef\Exceptions\ClefNotLogInException;

class Clef {

    private $app_id ;
    private $app_secret;
    private $APIBaseUri;

    private $authorizedResponse;
    private $userInfo;

    function __construct()
    {
        $this->app_id     = config('clef.api_id');
        $this->app_secret = config('clef.api_secret');
        $this->APIBaseUri = config('clef.APIBaseUri');
    }

    public function login($token, $code)
    {
        if($this->validateCsfrToken($token)) $this->ClefAuthorization($code);
    }

    public function fetchUserInfo()
    {
        if(!isset($this->authorizedResponse['success'])) throw new ClefNotLogInException('Clef have not successfully login.');
        $this->ClefUserInfo();
    }

    private function validateCsfrToken($token)
    {
        if ($token == csrf_token()) return true;
        return false;
    }

    private function ClefAuthorization($code)
    {
        $postdata = http_build_query(
            [
                'code' => $code,
                'app_id' => $this->app_id,
                'app_secret' => $this->app_secret
            ]
        );

        $opts = ['http' =>
                     [
                         'method'  => 'POST',
                         'header'  => 'Content-type: application/x-www-form-urlencoded',
                         'content' => $postdata
                     ]
        ];

        // get oauth code for the handshake
        $context  = stream_context_create($opts);
        $response = file_get_contents($this->APIBaseUri."authorize", false, $context);
        if(!$response) throw new ClefAuthorizationFailsException('Authorization Fails');
        $this->authorizedResponse = json_decode($response, true);
    }

    /**
     * @return mixed
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }

    private function ClefUserInfo()
    {
        $opts = ['http' =>
                     [
                         'method' => 'GET'
                     ]
        ];

        $url      = $this->APIBaseUri."info?access_token=" . $this->authorizedResponse['access_token'];
        $context  = stream_context_create($opts);
        $response = file_get_contents($url, false, $context);
        if (!$response) throw new ClefFetchUserInfoFailException('Cannot fetch user info');
        $response = json_decode($response, true);
        if(!isset($response['success'])) throw new ClefFetchUserInfoFailException('Cannot fetch user info');
        $this->userInfo = $response['info'];
    }


}
