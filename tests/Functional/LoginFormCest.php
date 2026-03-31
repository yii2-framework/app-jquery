<?php

declare(strict_types=1);

namespace app\tests\Functional;

use app\tests\Support\Fixtures\UserFixture;
use app\tests\Support\FunctionalTester;
use yii\helpers\Url;

/**
 * Functional tests for {@see \app\Controllers\SiteController::actionLogin()} login form.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
final class LoginFormCest
{
    public function _before(FunctionalTester $I): void
    {
        $I->amOnPage(Url::toRoute('/site/login'));
    }
    /**
     * @phpstan-return array{user: array{class: string, dataFile: string}}
     */
    public function _fixtures(): array
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                // @phpstan-ignore-next-line
                'dataFile' => codecept_data_dir() . 'login_data.php',
            ],
        ];
    }

    public function checkEmpty(FunctionalTester $I): void
    {
        $I->submitForm('#login-form', []);
        $I->seeValidationError('Username cannot be blank.');
        $I->seeValidationError('Password cannot be blank.');
    }

    public function checkInactiveAccount(FunctionalTester $I): void
    {
        $I->submitForm(
            '#login-form',
            [
                'LoginForm[username]' => 'test.test',
                'LoginForm[password]' => 'Test1234',
            ],
        );
        $I->seeValidationError('Incorrect username or password');
    }

    public function checkValidLogin(FunctionalTester $I): void
    {
        $I->submitForm(
            '#login-form',
            [
                'LoginForm[username]' => 'erau',
                'LoginForm[password]' => 'password_0',
            ],
        );
        $I->seeLink('Logout (erau)');
        $I->dontSeeLink('Login');
        $I->dontSeeLink('Signup');
    }

    public function checkWrongPassword(FunctionalTester $I): void
    {
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'erau',
            'LoginForm[password]' => 'wrong',
        ]);
        $I->seeValidationError('Incorrect username or password.');
    }
}
