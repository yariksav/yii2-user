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
            return User::findOne($this->id)->profile;
        };

        $this->actions = [
            'save' => [
                'type' => 'button',
                'before' => [$this, 'prepare'],
                'after' => [$this, 'verify'],
                'on' => function () {
                    $this->model->save();
                    $this->setAffect('profile', $this->id, 'update');
                }
            ],
        ];

        $this->controls = [
            'name'=>[
                'type' => 'text',
            ],
            'bio'=>[
                'type' => 'textarea',
            ],
        ];
    }
}