<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace yariksav\user\controllers;

use yariksav\user\models\Account;
use yariksav\user\models\User;
use yariksav\user\Module;
use Yii;
use yii\authclient\AuthAction;
use yii\authclient\ClientInterface;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;

/**
 * Controller that manages user authentication process.
 *
 * @property Module $module
 *
 * @author Savaryn Yaroslav <yariksav@gmail.com>
 */
class AuthController extends Controller
{

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    ['allow' => true, 'actions' => ['auth'], 'roles' => ['?']],
                ],
            ],
        ];
    }

    /** @inheritdoc */
    public function actions()
    {
        return [
            'auth' => [
                'class' => AuthAction::className(),
                // if user is not logged in, will try to log him in, otherwise
                // will try to connect social account to user.
                'successCallback' => Yii::$app->user->isGuest
                    ? [$this, 'authenticate']
                    : [$this, 'connect'],
            ],
        ];
    }



    /**
     * Tries to authenticate user via social network. If user has already used
     * this network's account, he will be logged in. Otherwise, it will try
     * to create new user account.
     *
     * @param ClientInterface $client
     */
    public function authenticate(ClientInterface $client)
    {
        $account = Account::findOrCreate($client);

//        $event = $this->getAuthEvent($account, $client);
//
//        $this->trigger(self::EVENT_BEFORE_AUTHENTICATE, $event);

        //if ($account->user instanceof User) {
            if ($account->user->isBlocked) {
                Yii::$app->session->setFlash('danger', Yii::t('user', 'Your account has been blocked.'));
                $this->action->successUrl = Url::to(['/user/security/login']);
            } else {
                Yii::$app->user->login($account->user, $this->module->rememberFor);
                $this->action->successUrl = Yii::$app->getUser()->getReturnUrl();
            }
//        } else {
//            $this->action->successUrl = $account->getConnectUrl();
//        }

//        $this->trigger(self::EVENT_AFTER_AUTHENTICATE, $event);
    }

    /**
     * Tries to connect social account to user.
     *
     * @param ClientInterface $client
     */
//    public function connect(ClientInterface $client)
//    {
//        /** @var Account $account */
//        $account = Yii::createObject(Account::className());
//        $event   = $this->getAuthEvent($account, $client);
//
//        $this->trigger(self::EVENT_BEFORE_CONNECT, $event);
//
//        $account->connectWithUser($client);
//
//        $this->trigger(self::EVENT_AFTER_CONNECT, $event);
//
//        $this->action->successUrl = Url::to(['/user/settings/networks']);
//    }
}
