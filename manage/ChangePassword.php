<?php

namespace yariksav\user\manage;

use yii;
use yariksav\actives\dialog\Dialog;
use yariksav\user\models\User;
use yariksav\user\helpers\Password;

class ChangePassword extends Dialog
{

    protected function _init() {
        $this->title = Yii::t('app', 'Change user password');
        $this->data = function() {
            if ($user = User::findOne($this->id)) {
                $user->password = '';
            }
            return $user;
        };

        $this->controls = [
            'username' => [
                'type' => 'label',
            ],
            'password' => [
                'type' => 'password',
                'validate'=>function($event){
                    if ($this->inputs['password'] != $this->inputs['passwordconfirm']) {
                        $event->sender->addError('password', Yii::t('app.error', 'Passwords is not match'));
                    }
                }
            ],
            'passwordconfirm' => [
                'label' => Yii::t('app', 'Confirm password'),
                'type' => 'password',
            ]
        ];

        $this->actions = [
            'save' => [
                'type' => 'button',
                'before' => [$this, 'prepare'],
                'on' => function () {
                    $this->model->password = $this->inputs['password'];
                    if ($this->saveModelInTransaction($this->model)) {
                        $this->message(Yii::t('app', 'Password successfully changed'));
                    }
                }
            ],
        ];
    }
}
?>