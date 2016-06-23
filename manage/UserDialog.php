<?php
namespace yariksav\user\manage;

use yii;
use yariksav\actives\dialog\StepDialog as Dialog;


class UserDialog extends Dialog {

    protected function _init(){
        $this->steps = [
            'account' => [
                'class'=>Account::className(),
                'actions'=>[
                    '*',
                    '+save'=>[
                        'type'=>$this->isNewRecord ? false : 'button',
                    ]
                ],
                'controls'=>[
                    '*',
                    'password'=>false
                ]
            ],
            'profile' => [
                'class'=>Profile::className(),
            ]
        ];
    }
}