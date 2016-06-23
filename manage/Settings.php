<?php
namespace yariksav\user\manage;

use yii;
use yariksav\actives\dialog\Dialog;
use yariksav\actives\dialog\StepDialog;



class Settings extends StepDialog {

    function _init(){
        $this->width = 1000;
        $this->permissions = '@';
        $this->stepRemember = true;
        $this->type = self::DIALOG_TYPE_GRIDS;

        $this->steps = [
            'users'=>[
                'title' => Yii::t('user', 'Users'),
                'class' => Dialog::className(),
                'controls' => [
                    'users'=> [
                        'type' => 'grid',
                        'config' => [
                            'class' => Users::className(),
                            'filter' => ['status' => 'A']
                        ]
                    ]
                ]
            ],

            // *************** USER Rights ******************
            'roles'=>[
                'title' => Yii::t('user', 'User roles'),
                'class' => Dialog::className(),
                'controls' => [
                    'roles' => [
                        'type'=>'grid',
                        'config' => [
                            'class' => UserRoles::className(),
                        ]
                    ],
                ]
            ]
        ];
    }
}