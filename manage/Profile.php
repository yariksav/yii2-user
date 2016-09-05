<?php

namespace yariksav\user\manage;

use yii;
use yariksav\actives\dialog\Dialog;
use yariksav\user\models\User;

class Profile extends Dialog
{
    public $width = 500;

    protected function _init() {

        $this->title = Yii::t('user', 'Profile');
        //$this->permissions = 'user password change';

        $this->data = function(){
            return User::findOne($this->key)->profile;
        };

        $this->actions = [
            'save' => [
                'type' => 'button',
                'before' => [$this, 'prepare'],
                'after' => [$this, 'verify'],
                'on' => function () {
                    $this->model->save();
                    $this->emit('profile');
                }
            ],
        ];

        $this->controls = [
            'name'=>[
                'type' => 'text',
            ],
            'info'=>[
                'type' => 'textarea',
            ],
        ];
    }
}