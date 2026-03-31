<?php

declare(strict_types=1);

namespace app\tests\Functional;

use app\Models\User;
use app\tests\Support\Fixtures\UserFixture;
use app\tests\Support\FunctionalTester;
use yii\helpers\Url;

/**
 * Functional tests for {@see \app\Controllers\SiteController::actionRequestPasswordReset()} and
 * {@see \app\Controllers\SiteController::actionResetPassword()}.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
final class PasswordResetCest
{
    /**
     * @phpstan-return array{user: array{class: string, dataFile: string}}
     */
    public function _fixtures(): array
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                // @phpstan-ignore binaryOp.invalid
                'dataFile' => codecept_data_dir() . 'user.php',
            ],
        ];
    }

    public function requestResetWithEmptyEmail(FunctionalTester $I): void
    {
        $I->amOnPage(Url::toRoute('/site/request-password-reset'));
        $I->see('Reset your password', 'h1');
        $I->submitForm('#request-password-reset-form', []);
        $I->seeValidationError('Email cannot be blank.');
    }

    public function requestResetWithWrongEmail(FunctionalTester $I): void
    {
        $I->amOnPage(Url::toRoute('/site/request-password-reset'));
        $I->submitForm('#request-password-reset-form', [
            'PasswordResetRequestForm[email]' => 'nonexistent@example.com',
        ]);
        $I->seeValidationError('There is no user with this email address.');
    }

    public function requestResetSuccessfully(FunctionalTester $I): void
    {
        $I->amOnPage(Url::toRoute('/site/request-password-reset'));
        $I->submitForm('#request-password-reset-form', [
            'PasswordResetRequestForm[email]' => 'brady.renner@rutherford.com',
        ]);
        $I->seeEmailIsSent();
        $I->see('Check your email for further instructions.');
    }

    public function resetPasswordWithValidToken(FunctionalTester $I): void
    {
        /** @var User $user */
        $user = User::findByUsername('okirlin');

        /** @phpstan-var string $token */
        $token = $user->password_reset_token;

        $I->amOnPage(Url::toRoute(['/site/reset-password', 'token' => $token]));
        $I->see('Set your new password', 'h1');
        $I->submitForm('#reset-password-form', [
            'ResetPasswordForm[password]' => 'newpassword123',
        ]);
        $I->see('New password saved.');
    }

    public function resetPasswordWithInvalidToken(FunctionalTester $I): void
    {
        $I->amOnPage(Url::toRoute(['/site/reset-password', 'token' => 'invalid_token_123']));
        $I->canSee('Wrong password reset token.');
    }
}
