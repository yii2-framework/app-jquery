<?php

declare(strict_types=1);

namespace app\tests\functional;

use app\tests\support\Fixtures\UserFixture;
use app\tests\support\FunctionalTester;
use yii\helpers\Url;

/**
 * Functional tests for {@see \app\controllers\UserController::actionIndex()} GridView page.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
final class UserGridCest
{
    /**
     * @phpstan-return array{user: array{class: string, dataFile: string}}
     */
    public function _fixtures(): array
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                // @phpstan-ignore-next-line
                'dataFile' => codecept_data_dir() . 'user.php',
            ],
        ];
    }

    public function checkGridViewDisplaysUserData(FunctionalTester $I): void
    {
        $I->amOnPage(Url::toRoute('/user/login'));
        $I->submitForm(
            '#login-form',
            [
                'LoginForm[username]' => 'okirlin',
                'LoginForm[password]' => 'password_0',
            ],
        );

        $I->amOnPage(Url::toRoute('/user/index'));
        $I->see('okirlin');
        $I->see('okirlin@example.com');
        $I->see('Active');
    }

    public function checkGridViewFilterByUsername(FunctionalTester $I): void
    {
        $I->amOnPage(Url::toRoute('/user/login'));
        $I->submitForm(
            '#login-form',
            [
                'LoginForm[username]' => 'okirlin',
                'LoginForm[password]' => 'password_0',
            ],
        );

        $I->amOnPage(Url::toRoute(['/user/index', 'UserSearch[username]' => 'okirlin']));
        $I->see('okirlin');
        $I->dontSee('troy.becker');
    }

    public function checkGridViewFilterWithInvalidData(FunctionalTester $I): void
    {
        $I->amOnPage(Url::toRoute('/user/login'));
        $I->submitForm(
            '#login-form',
            [
                'LoginForm[username]' => 'okirlin',
                'LoginForm[password]' => 'password_0',
            ],
        );

        $I->amOnPage(Url::toRoute(['/user/index', 'UserSearch[id]' => 'invalid']));
        $I->see('No results found.', '.empty');
    }

    public function checkGridViewRendersAfterLogin(FunctionalTester $I): void
    {
        $I->amOnPage(Url::toRoute('/user/login'));
        $I->submitForm(
            '#login-form',
            [
                'LoginForm[username]' => 'okirlin',
                'LoginForm[password]' => 'password_0',
            ],
        );

        $I->amOnPage(Url::toRoute('/user/index'));
        $I->see('Users', 'h1');
        $I->seeElement('table');
        $I->see('USERNAME');
        $I->see('EMAIL');
        $I->see('STATUS');
        $I->see('JOINED');
    }

    public function checkGuestRedirectsToLogin(FunctionalTester $I): void
    {
        $I->amOnPage(Url::toRoute('/user/index'));
        $I->seeInCurrentUrl('user%2Flogin');
    }
}
