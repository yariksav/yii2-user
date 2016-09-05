<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace yariksav\user;

use Yii;
use yii\authclient\Collection;
use yii\base\BootstrapInterface;
use yii\console\Application as ConsoleApplication;
use yii\i18n\PhpMessageSource;

/**
 * Bootstrap class registers module and user application component. It also creates some url rules which will be applied
 * when UrlManager.enablePrettyUrl is enabled.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Bootstrap implements BootstrapInterface
{
    /** @var array Model's map */
    private $_modelMap = [
        'User'             => 'yariksav\user\models\User',
        'Account'          => 'yariksav\user\models\Account',
        'Profile'          => 'yariksav\user\models\Profile',
        'Token'            => 'yariksav\user\models\Token'
    ];

    /** @inheritdoc */
    public function bootstrap($app)
    {
        /** @var Module $module */
        /** @var \yii\db\ActiveRecord $modelName */
        if ($app->hasModule('user') && ($module = $app->getModule('user')) instanceof Module) {
            $this->_modelMap = array_merge($this->_modelMap, $module->modelMap);

            foreach ($this->_modelMap as $name => $definition) {
                $class = "yariksav\\user\\models\\" . $name;
                Yii::$container->set($class, $definition);
                $modelName = is_array($definition) ? $definition['class'] : $definition;
                $module->modelMap[$name] = $modelName;
//                if (in_array($name, ['User', 'Profile', 'Token', 'Account'])) {
//                    Yii::$container->set($name . 'Query', function () use ($modelName) {
//                        return $modelName::find();
//                    });
//                }
            }
//
//            Yii::$container->setSingleton(Finder::className(), [
//                'userQuery'    => Yii::$container->get('UserQuery'),
//                'profileQuery' => Yii::$container->get('ProfileQuery'),
//                'tokenQuery'   => Yii::$container->get('TokenQuery'),
//                'accountQuery' => Yii::$container->get('AccountQuery'),
//            ]);

            if ($app instanceof ConsoleApplication) {
                $module->controllerNamespace = 'yariksav\user\commands';
            } else {
                Yii::$container->set('yii\web\User', [
                    //'enableAutoLogin' => true,
                    //'loginUrl'        => ['/user/security/login'],
                    'identityClass'   => $module->modelMap['User'],
                ]);

//                $configUrlRule = [
//                    'prefix' => $module->urlPrefix,
//                    'rules'  => $module->urlRules,
//                ];
//
//                if ($module->urlPrefix != 'user') {
//                    $configUrlRule['routePrefix'] = 'user';
//                }
//
//                $configUrlRule['class'] = 'yii\web\GroupUrlRule';
//                $rule = Yii::createObject($configUrlRule);
//
//                $app->urlManager->addRules([$rule], false);

                if (!$app->has('authClientCollection')) {
                    $app->set('authClientCollection', [
                        'class' => Collection::className(),
                    ]);
                }
            }

            $app->params['yii.migrations'][] = '@yariksav/yii2-user/migrations';

            if (!isset($app->get('i18n')->translations['user*'])) {
                $app->get('i18n')->translations['user*'] = [
                    'class'    => PhpMessageSource::className(),
                    'basePath' => __DIR__ . '/messages',
                    'sourceLanguage' => 'en-US'
                ];
            }

            if (!isset($app->get('i18n')->translations['roles*'])) {
                $app->get('i18n')->translations['roles*'] = [
                    'class'    => PhpMessageSource::className(),
                    'basePath' => __DIR__ . '/messages',
                    'sourceLanguage' => 'en-US'
                ];
            }
            //Yii::$container->set('dektrium\user\Mailer', $module->mailer);
        }
    }
}
