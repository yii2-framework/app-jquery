<?php

declare(strict_types=1);

namespace yii\demo\basic\tests\Unit\Models;

use Yii;
use yii\base\Security;
use yii\demo\basic\Models\LoginForm;

final class LoginFormTest extends \Codeception\Test\Unit
{
    private $_model;

    public function testLoginCorrect()
    {
        $this->_model = new LoginForm(
            new Security(),
            [
                'username' => 'demo',
                'password' => 'demo',
            ],
        );

        verify($this->_model->login())->true();
        verify(Yii::$app->user->isGuest)->false();
        verify($this->_model->errors)->arrayHasNotKey('password');
    }

    public function testLoginNoUser()
    {
        $this->_model = new LoginForm(
            new Security(),
            [
                'username' => 'not_existing_username',
                'password' => 'not_existing_password',
            ],
        );

        verify($this->_model->login())->false();
        verify(Yii::$app->user->isGuest)->true();
    }

    public function testLoginWrongPassword()
    {
        $this->_model = new LoginForm(
            new Security(),
            [
                'username' => 'demo',
                'password' => 'wrong_password',
            ],
        );

        verify($this->_model->login())->false();
        verify(Yii::$app->user->isGuest)->true();
        verify($this->_model->errors)->arrayHasKey('password');
    }

    protected function _after()
    {
        Yii::$app->user->logout();
    }
}
