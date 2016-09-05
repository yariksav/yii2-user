<?php
namespace yariksav\user\manage;

use yii;
use yariksav\actives\dialog\Dialog;
use yariksav\actives\dialog\StepDialog;


class Settings extends StepDialog {

    function _init(){
        $this->width = 1000;
        $this->permissions = '@';
        $this->steps->remember = true;
        $this->type = self::DIALOG_TYPE_GRIDS;

        $this->steps = [
            'users'=>[
                'class'=>Dialog::className(),
                'title'=>Yii::t('user', 'Users'),
                'controls'=>[
                    'users'=>[
                        'type'=>'grid',
                        'config'=>[
                            'class'=>Users::className(),
                            'plugins'=>[
                                'filter'=>[
                                    'value'=>[
                                        'status'=>'A'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],

            // *************** USER Rights ******************
            'roles'=>[
                'class' => Dialog::className(),
                'title' => Yii::t('user', 'User roles'),
                'width' => 700,
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