<?php

declare(strict_types=1);

namespace app\tests\Unit\Models;

use app\Models\LoginForm;
use Yii;
use yii\base\Security;

/**
 * Unit tests for {@see \app\Models\LoginForm} model.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
final class LoginFormTest extends \Codeception\Test\Unit
{
    private LoginForm|null $model = null;

    public function testLoginCorrect(): void
    {
        $this->model = new LoginForm(
            new Security(),
            [
                'username' => 'demo',
                'password' => 'demo',
            ],
        );

        verify($this->model->login())->true();
        verify(Yii::$app->user->isGuest)->false();
        verify($this->model->errors)->arrayHasNotKey('password');
    }

    public function testLoginNoUser(): void
    {
        $this->model = new LoginForm(
            new Security(),
            [
                'username' => 'not_existing_username',
                'password' => 'not_existing_password',
            ],
        );

        verify($this->model->login())->false();
        verify(Yii::$app->user->isGuest)->true();
    }

    public function testLoginWrongPassword(): void
    {
        $this->model = new LoginForm(
            new Security(),
            [
                'username' => 'demo',
                'password' => 'wrong_password',
            ],
        );

        verify($this->model->login())->false();
        verify(Yii::$app->user->isGuest)->true();
        verify($this->model->errors)->arrayHasKey('password');
    }

    protected function _after(): void
    {
        Yii::$app->user->logout();
    }
}
