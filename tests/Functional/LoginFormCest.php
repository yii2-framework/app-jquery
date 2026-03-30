<?php

declare(strict_types=1);

namespace app\tests\Functional;

use app\Models\User;
use app\tests\Support\FunctionalTester;

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
        $I->amOnRoute('site/login');
    }

    // demonstrates `amLoggedInAs` method
    public function internalLoginById(FunctionalTester $I): void
    {
        $I->amLoggedInAs(100);
        $I->amOnPage('/');
        $I->see('Logout (admin)');
    }

    // demonstrates `amLoggedInAs` method
    public function internalLoginByInstance(FunctionalTester $I): void
    {
        $user = User::findByUsername('admin');

        $I->assertNotNull($user);
        $I->amLoggedInAs($user);
        $I->amOnPage('/');
        $I->see('Logout (admin)');
    }

    public function loginSuccessfully(FunctionalTester $I): void
    {
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'admin',
            'LoginForm[password]' => 'admin',
        ]);
        $I->see('Logout (admin)');
        $I->dontSeeElement('form#login-form');
    }

    public function loginWithEmptyCredentials(FunctionalTester $I): void
    {
        $I->submitForm('#login-form', []);
        $I->expectTo('see validation errors');
        $I->see('Username cannot be blank.');
        $I->see('Password cannot be blank.');
    }

    public function loginWithWrongCredentials(FunctionalTester $I): void
    {
        $I->submitForm(
            '#login-form',
            [
                'LoginForm[username]' => 'admin',
                'LoginForm[password]' => 'wrong',
            ],
        );
        $I->expectTo('see validation errors');
        $I->see('Incorrect username or password.');
    }


    public function openLoginPage(FunctionalTester $I): void
    {
        $I->see('Login', 'h1');
    }

    public function redirectWhenAlreadyLoggedIn(FunctionalTester $I): void
    {
        $I->amLoggedInAs(100);
        $I->amOnRoute('site/login');
        $I->dontSee('Login', 'h1');
        $I->see('My Yii Application');
    }
}
