<?php
/**
 * Created by PhpStorm.
 * User: Savaryn Yaroslav
 * Date: 25.05.2016
 * Time: 20:39
 */

namespace yariksav\user\auth;

use yii;
use yariksav\actives\dialog\Dialog;

class ForgotPassword extends Dialog {

    protected function _init() {
        $this->title = Yii::t('app', 'Forgot password');
        $this->controls = [
            'email'=>[
                'type' => 'text',
                'label' => Yii::t('app', 'Email')
            ],
        ];
        $this->actions = [

        ];
    }
}