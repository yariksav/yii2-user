<?php
namespace yariksav\user\auth;

use dektrium\user\helpers\Password;
use yariksav\actives\dialog\ValidationException;
use yii;
use yii\helpers\Url;
use app\models\User;
use yariksav\actives\dialog\Dialog;

class Login extends Dialog {

    function _init(){
        $this->title = Yii::t('app', 'Login');
        $this->permissions = '*';
        $this->actions = [
            'forgotpassword'=>[
                'type'=>'link',
                'title'=>Yii::t('app', 'Forgot password'),
                'on'=>function(){
                    $this->redirect(['class'=>ForgotPassword::className()]);
                }
            ],
            'register'=>[
                'type'=>'link',
                'title'=>Yii::t('app', 'Register'),
                'icon'=>'fa fa-user-plus',
                'on'=>function(){
                    $this->redirect(['class'=>Register::className()]);
                }
            ],
            'login' => [
                'type'=>'button',
                'title'=>Yii::t('app', 'Login'),
                'icon'=>'fa fa-sign-in',
                'options'=>['class'=>'btn btn-success'],
                'before'=>[$this, 'prepare'],
                'on'=>function() {
                    $username = $this->inputs['username'];
                    $password = Password::hash($this->inputs['password']);

                    $user = \dektrium\user\models\User::find()->andFilterWhere(['or',
                        ['=', 'username', $username],
                        ['=', 'email', $username],
                    ])->one();

                    if (!$user || !Password::validate($this->inputs['password'], $user->password_hash)) {
                        throw new ValidationException(['username' => Yii::t('user', 'Invalid login or password')]);
                    }

                    Yii::$app->user->login($user, $this->inputs['rememberMe'] ? 3600*24*30 : 0);
                    $this->response->fullReload = true;//'location="/'.Url::base().'"';
                }
            ],
            'logout'=>[
                'on'=>function() {
                    if (Yii::$app->user->isGuest) {
                        return;
                    }
                    if ($this->confirm(\Yii::t('user', 'Do you want to exit application?'))){
                        Yii::$app->user->logout();
                        $this->response->fullReload = true;
                    }
                }
            ],

        ];

        $this->controls = [
            'username'=>[
                'type' => 'text',
                'label' => Yii::t('app', 'Username')
            ],
            'password'=>[
                'type' => 'password',
                'label'=> Yii::t('app', 'Password')
            ],
            'rememberMe'=>[
                'type' => 'toggler',
                'label' => Yii::t('app', 'Remember me'),
                'collection' => [
                    false => ['text'=>Yii::t('app', 'No'), 'class'=>'default'],
                    true => Yii::t('app', 'Yes'),
                ],

            ],
            'social'=>[
                'type'=>'auth',
                'value'=>function() {
                    return yii\authclient\widgets\AuthChoice::widget([
                        'baseAuthUrl'=>['/user/auth/auth']
                    ]);
                }
            ]
        ];
    }
}

?>