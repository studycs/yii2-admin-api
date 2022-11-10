<?php

namespace app\handlers;

class HttpPublicAuth extends \yii\filters\auth\HttpBearerAuth
{
    public $header = 'Authorization';

    public function authenticate($user, $request, $response){
        $authHeader = $request->getHeaders()->get($this->header);
        if($authHeader==null) $authHeader = $_COOKIE[$this->header] ?? null;
        if ($authHeader !== null){
            if ($this->pattern !== null && preg_match($this->pattern, $authHeader, $matches)) $authHeader = $matches[1];
            $identity = $user->loginByAccessToken($authHeader, get_class($this));
            if ($identity === null) {
                $this->challenge($response);
                $this->handleFailure($response);
            }
            return $identity;
        }
        return null;
    }

}