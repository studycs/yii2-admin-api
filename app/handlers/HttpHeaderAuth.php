<?php

namespace app\handlers;

use yii\base\UserException;

class HttpHeaderAuth extends \yii\filters\auth\HttpHeaderAuth
{
    public $header = 'Authorization';

    public $optional = [];

    public $safe = ['/site/login','/site/error','/auth/user','/auth/userinfo','/auth/role-menu','/auth/gv-groups',
        '/auth/receive-groups','/auth/message-group','/auth/dict-gv','/system/password'
    ];

    public function authenticate($user, $request, $response)
    {
        $authHeader = $request->getHeaders()->get($this->header);
        if($authHeader==null) $authHeader = $_COOKIE[$this->header] ?? null;
        if ($authHeader !== null) {
            if ($this->pattern !== null) {
                if (preg_match($this->pattern, $authHeader, $matches)) {
                    $authHeader = $matches[1];
                } else {
                    return null;
                }
            }
            $identity = $user->loginByAccessToken($authHeader, get_class($this));
            if ($identity === null) {
                $this->challenge($response);
                $this->handleFailure($response);
            }

            return $identity;
        }

        return null;
    }

    /**
     * @param $event
     * @return void
     * @throws UserException
     */
    public function afterFilter($event)
    {
        $actionId = '/' . $event->action->controller->route;
        if(!in_array($actionId,$this->safe) && !\yii::$app->user->can($actionId)){
            throw new UserException(\Yii::t('yii', 'You are not allowed to perform this action.'),403);
        }
    }

}