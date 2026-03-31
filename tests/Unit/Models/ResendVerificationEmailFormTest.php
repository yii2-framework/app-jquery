<?php

declare(strict_types=1);

namespace app\tests\Unit\Models;

use app\Models\ResendVerificationEmailForm;
use app\tests\Support\Fixtures\UserFixture;
use app\tests\Support\UnitTester;
use Yii;
use yii\mail\MessageInterface;

/**
 * Unit tests for {@see \app\Models\ResendVerificationEmailForm} model.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
final class ResendVerificationEmailFormTest extends \Codeception\Test\Unit
{
    protected UnitTester|null $tester = null;

    public function _before(): void
    {
        $this->tester?->haveFixtures(
            [
                'user' => [
                    'class' => UserFixture::class,
                    // @phpstan-ignore-next-line
                    'dataFile' => codecept_data_dir() . 'user.php',
                ],
            ],
        );
    }

    public function testEmptyEmailAddress(): void
    {
        $model = new ResendVerificationEmailForm();

        $model->attributes = ['email' => ''];

        verify($model->validate())
            ->false(
                'Failed asserting that validation fails for an empty email.',
            );
        verify($model->hasErrors())
            ->true(
                'Failed asserting that validation errors are present.',
            );
        verify($model->getFirstError('email'))
            ->equals(
                'Email cannot be blank.',
                'Failed asserting that the blank email error message is correct.',
            );
    }

    public function testResendToActiveUser(): void
    {
        $model = new ResendVerificationEmailForm();

        $model->attributes = ['email' => 'test2@mail.com'];

        verify($model->validate())
            ->false(
                'Failed asserting that validation fails for an already active user.',
            );
        verify($model->hasErrors())
            ->true(
                'Failed asserting that validation errors are present.',
            );
        verify($model->getFirstError('email'))
            ->equals(
                'There is no user with this email address.',
                'Failed asserting that active user email is rejected by the inactive-only filter.',
            );
    }

    public function testSendEmailToNonExistingInactiveUser(): void
    {
        $model = new ResendVerificationEmailForm();

        $model->email = 'nonexistent@example.com';

        /** @phpstan-var string $supportEmail */
        $supportEmail = Yii::$app->params['supportEmail'] ?? '';

        verify($model->sendEmail(Yii::$app->mailer, $supportEmail, Yii::$app->name))
            ->false(
                "Failed asserting that sendEmail returns 'false' when inactive user is not found.",
            );
    }

    public function testSuccessfullyResend(): void
    {
        $model = new ResendVerificationEmailForm();

        $model->attributes = ['email' => 'test@mail.com'];

        verify($model->validate())
            ->true(
                'Failed asserting that validation passes for an inactive user email.',
            );
        verify($model->hasErrors())
            ->false(
                'Failed asserting that no validation errors are present.',
            );

        /** @phpstan-var string $supportEmail */
        $supportEmail = Yii::$app->params['supportEmail'] ?? '';

        verify($model->sendEmail(Yii::$app->mailer, $supportEmail, Yii::$app->name))
            ->true(
                'Failed asserting that verification email is resent successfully.',
            );

        $this->tester?->seeEmailIsSent();

        /** @phpstan-var MessageInterface|null $mail */
        $mail = $this->tester?->grabLastSentEmail();

        verify($mail)
            ->instanceOf(
                MessageInterface::class,
                'Failed asserting that a verification email was captured.',
            );
        verify($mail?->getTo())
            ->arrayHasKey(
                'test@mail.com',
                'Failed asserting that email is sent to the inactive user.',
            );
        verify($mail?->getFrom())
            ->arrayHasKey(
                $supportEmail,
                "Failed asserting that email is sent 'from' the support address.",
            );
        verify($mail?->getSubject())
            ->equals(
                'Account registration at ' . Yii::$app->name,
                "Failed asserting that email 'subject' matches the registration template.",
            );

        /** @phpstan-var \app\Models\User $user */
        $user = \app\Models\User::findOne(['username' => 'test.test']);

        /** @phpstan-var \yii\symfonymailer\Message $mail */
        verify($mail->getSymfonyEmail()->getTextBody())
            ->stringContainsString(
                $user->verification_token ?? '',
                "Failed asserting that email 'body' contains the verification 'token'.",
            );
    }

    public function testWrongEmailAddress(): void
    {
        $model = new ResendVerificationEmailForm();

        $model->attributes = ['email' => 'aaa@bbb.cc'];

        verify($model->validate())
            ->false(
                'Failed asserting that validation fails for a non-existing email.',
            );
        verify($model->hasErrors())
            ->true(
                'Failed asserting that validation errors are present.',
            );
        verify($model->getFirstError('email'))
            ->equals(
                'There is no user with this email address.',
                'Failed asserting that the error message matches for a non-existing email.',
            );
    }
}
