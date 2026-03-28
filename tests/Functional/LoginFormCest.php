<?php

declare(strict_types=1);

namespace yii\demo\basic\tests\Functional;

use yii\demo\basic\Models\User;
use yii\demo\basic\tests\Support\FunctionalTester;

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
        /** @var User $user */
        $user = User::findByUsername('admin');

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
        $I->expectTo('see validations errors');
        $I->see('Username cannot be blank.');
        $I->see('Password cannot be blank.');
    }

    public function loginWithWrongCredentials(FunctionalTester $I): void
    {
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'admin',
            'LoginForm[password]' => 'wrong',
        ]);
        $I->expectTo('see validations errors');
        $I->see('Incorrect username or password.');
    }

    public function openLoginPage(FunctionalTester $I): void
    {
        $I->see('Login', 'h1');
    }
}
