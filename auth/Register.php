<?php
/**
 * Created by PhpStorm.
 * User: Savaryn Yaroslav
 * Date: 25.05.2016
 * Time: 20:39
 */

namespace yariksav\user\auth;

use yariksav\user\models\User;
use yii;
use yariksav\actives\dialog\Dialog;

class Register extends Dialog {

    protected function _init() {
        $this->title = Yii::t('user', 'Register');
        $this->controls = [
            'username'=>[
                'type' => 'text',
                //'label' => Yii::t('app', 'Username')
            ],
            'email'=>[
                'type' => 'text',
            ],
            'password'=>[
                'type' => 'password',
                //'label'=> Yii::t('app', 'Password')
            ],
            'name'=>[
                'type' => 'text',
                'label'=> Yii::t('app', 'First and Last name')
            ],
        ];
        $this->actions = [
            'register' => [
                'type'=>'button',
                'title'=>Yii::t('user', 'Sign up'),
                'icon'=>'fa fa-user-plus',
                'before'=>[$this, 'prepare'],
                'on'=>function() {
//                    $username = $this->inputs['username'];
//                    $password = Password::hash($this->inputs['password']);
//
//                    $user = \dektrium\user\models\User::find()->andFilterWhere(['or',
//                        ['=', 'username', $username],
//                        ['=', 'email', $username],
//                    ])->one();
//
//                    if (!$user || !Password::validate($this->inputs['password'], $user->password_hash)) {
//                        throw new ValidationException(['username' => Yii::t('user', 'Invalid login or password')]);
//                    }
//
//                    Yii::$app->user->login($user, $this->inputs['rememberMe'] ? 3600*24*30 : 0);
//                    $this->response->script = 'location="/'.Url::base().'"';
                }
            ],
        ];
        $this->data = function() {
            $user = new User();
            $user->scenario = 'register';
            return $user;
        };
    }
}