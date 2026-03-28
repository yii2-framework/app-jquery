<?php

declare(strict_types=1);

namespace yii\demo\basic\tests\Acceptance;

use yii\demo\basic\tests\Support\AcceptanceTester;
use yii\helpers\Url;

final class LoginCest
{
    public function ensureThatLoginWorks(AcceptanceTester $I): void
    {
        $I->amOnPage(Url::toRoute('/site/login'));
        $I->see('Login', 'h1');

        $I->amGoingTo('try to login with correct credentials');
        $I->fillField('input[name="LoginForm[username]"]', 'admin');
        $I->fillField('input[name="LoginForm[password]"]', 'admin');
        $I->click('button[name="login-button"]');

        $I->expectTo('see user info');
        $I->see('Logout');
    }
}
