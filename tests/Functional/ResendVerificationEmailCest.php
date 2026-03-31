<?php

declare(strict_types=1);

namespace app\tests\Functional;

use app\Models\User;
use app\tests\Support\Fixtures\UserFixture;
use app\tests\Support\FunctionalTester;
use yii\helpers\Url;

/**
 * Functional tests for {@see \app\Controllers\SiteController::actionResendVerificationEmail()}.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
final class ResendVerificationEmailCest
{
    private string $formId = '#resend-verification-email-form';

    public function _before(FunctionalTester $I): void
    {
        $I->amOnPage(Url::toRoute('/site/resend-verification-email'));
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
                'dataFile' => codecept_data_dir() . 'user.php',
            ],
        ];
    }

    public function checkAlreadyVerifiedEmail(FunctionalTester $I): void
    {
        $I->submitForm($this->formId, ['ResendVerificationEmailForm[email]' => 'test2@mail.com']);
        $I->seeValidationError('There is no user with this email address.');
    }

    public function checkEmptyField(FunctionalTester $I): void
    {
        $I->submitForm($this->formId, ['ResendVerificationEmailForm[email]' => '']);
        $I->seeValidationError('Email cannot be blank.');
    }

    public function checkPage(FunctionalTester $I): void
    {
        $I->see('Resend verification email', 'h1');
        $I->see('Enter your email to receive a new verification link');
    }

    public function checkSendSuccessfully(FunctionalTester $I): void
    {
        $I->submitForm($this->formId, ['ResendVerificationEmailForm[email]' => 'test@mail.com']);
        $I->canSeeEmailIsSent();
        $I->seeRecord(
            User::class,
            [
                'email' => 'test@mail.com',
                'username' => 'test.test',
                'status' => User::STATUS_INACTIVE,
            ],
        );
        $I->see('Check your email for further instructions.');
    }

    public function checkWrongEmail(FunctionalTester $I): void
    {
        $I->submitForm($this->formId, ['ResendVerificationEmailForm[email]' => 'wrong@email.com']);
        $I->seeValidationError('There is no user with this email address.');
    }

    public function checkWrongEmailFormat(FunctionalTester $I): void
    {
        $I->submitForm($this->formId, ['ResendVerificationEmailForm[email]' => 'abcd.com']);
        $I->seeValidationError('Email is not a valid email address.');
    }
}
