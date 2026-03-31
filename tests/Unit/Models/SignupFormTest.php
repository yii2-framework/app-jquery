<?php

declare(strict_types=1);

namespace app\tests\Unit\Models;

use app\Models\SignupForm;
use app\Models\User;
use app\tests\Support\Fixtures\UserFixture;
use app\tests\Support\UnitTester;
use Yii;
use yii\mail\MessageInterface;

/**
 * Unit tests for {@see \app\Models\SignupForm} model.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
final class SignupFormTest extends \Codeception\Test\Unit
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

    public function testCorrectSignup(): void
    {
        $model = new SignupForm(
            [
                'username' => 'some_username',
                'email' => 'some_email@example.com',
                'password' => 'some_password',
            ],
        );

        /** @phpstan-var string $supportEmail */
        $supportEmail = Yii::$app->params['supportEmail'] ?? '';

        $user = $model->signup(Yii::$app->mailer, $supportEmail, Yii::$app->name);

        verify($user)
            ->notEmpty(
                'Failed asserting that signup returns a truthy value on success.',
            );

        /** @var User $user */
        $user = $this->tester?->grabRecord(
            User::class,
            [
                'username' => 'some_username',
                'email' => 'some_email@example.com',
                'status' => User::STATUS_INACTIVE,
            ],
        );

        $this->tester?->seeEmailIsSent();

        /** @phpstan-var MessageInterface|null $mail */
        $mail = $this->tester?->grabLastSentEmail();

        verify($mail)
            ->instanceOf(
                MessageInterface::class,
                'Failed asserting that a verification email was sent.',
            );
        verify($mail?->getTo())
            ->arrayHasKey(
                'some_email@example.com',
                'Failed asserting that email is sent to the registered address.',
            );
        verify($mail?->getFrom())
            ->arrayHasKey(
                $supportEmail,
                'Failed asserting that email is sent from the support address.',
            );
        verify($mail?->getSubject())
            ->equals(
                'Account registration at ' . Yii::$app->name,
                'Failed asserting that email subject matches the registration template.',
            );
        /** @phpstan-var \yii\symfonymailer\Message $mail */
        verify($mail->getSymfonyEmail()->getTextBody())
            ->stringContainsString(
                $user->verification_token ?? '',
                'Failed asserting that email body contains the verification token.',
            );
    }

    public function testNotCorrectSignup(): void
    {
        $model = new SignupForm(
            [
                'username' => 'troy.becker',
                'email' => 'nicolas.dianna@hotmail.com',
                'password' => 'some_password',
            ],
        );

        /** @phpstan-var string $supportEmail */
        $supportEmail = Yii::$app->params['supportEmail'] ?? '';

        verify($model->signup(Yii::$app->mailer, $supportEmail, Yii::$app->name))
            ->empty(
                'Failed asserting that signup fails with duplicate username and email.',
            );
        verify($model->getErrors('username'))
            ->notEmpty(
                'Failed asserting that a username validation error is present.',
            );
        verify($model->getErrors('email'))
            ->notEmpty(
                'Failed asserting that an email validation error is present.',
            );
        verify($model->getFirstError('username'))
            ->equals(
                'This username has already been taken.',
                'Failed asserting that the username uniqueness error message is correct.',
            );
        verify($model->getFirstError('email'))
            ->equals(
                'This email address has already been taken.',
                'Failed asserting that the email uniqueness error message is correct.',
            );
    }
}
