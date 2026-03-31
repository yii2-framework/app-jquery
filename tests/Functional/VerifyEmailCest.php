<?php

declare(strict_types=1);

namespace app\tests\Functional;

use app\Models\User;
use app\tests\Support\Fixtures\UserFixture;
use app\tests\Support\FunctionalTester;
use yii\helpers\Url;

/**
 * Functional tests for {@see \app\Controllers\SiteController::actionVerifyEmail()} email verification.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
final class VerifyEmailCest
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

    public function checkAlreadyActivatedToken(FunctionalTester $I): void
    {
        $I->amOnPage(Url::toRoute(['/site/verify-email', 'token' => 'already_used_token_1548675330']));
        $I->canSee('Wrong verify email token.');
    }

    public function checkEmptyToken(FunctionalTester $I): void
    {
        $I->amOnPage(Url::toRoute(['/site/verify-email', 'token' => '']));
        $I->canSee('Verify email token cannot be blank.');
    }

    public function checkInvalidToken(FunctionalTester $I): void
    {
        $I->amOnPage(Url::toRoute(['/site/verify-email', 'token' => 'wrong_token']));
        $I->canSee('Wrong verify email token.');
    }

    public function checkNoToken(FunctionalTester $I): void
    {
        $I->amOnPage(Url::toRoute('/site/verify-email'));
        $I->canSee('Missing required parameters: token');
    }

    public function checkSuccessVerification(FunctionalTester $I): void
    {
        $I->amOnPage(Url::toRoute(['/site/verify-email', 'token' => '4ch0qbfhvWwkcuWqjN8SWRq72SOw1KYT_1548675330']));
        $I->canSee('Your email has been confirmed!');
        $I->seeRecord(
            User::class,
            [
                'username' => 'test.test',
                'email' => 'test@mail.com',
                'status' => User::STATUS_ACTIVE,
            ],
        );
    }
}
