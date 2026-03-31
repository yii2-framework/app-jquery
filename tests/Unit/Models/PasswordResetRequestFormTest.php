<?php

declare(strict_types=1);

namespace app\tests\Unit\Models;

use app\Models\PasswordResetRequestForm;
use app\Models\User;
use app\tests\Support\Fixtures\UserFixture;
use app\tests\Support\UnitTester;
use Yii;
use yii\base\Event;
use yii\base\ModelEvent;
use yii\db\BaseActiveRecord;
use yii\mail\MessageInterface;

/**
 * Unit tests for {@see \app\Models\PasswordResetRequestForm} model.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
final class PasswordResetRequestFormTest extends \Codeception\Test\Unit
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

    public function testNotSendEmailsToInactiveUser(): void
    {
        $model = new PasswordResetRequestForm();

        $model->email = 'troy.becker@example.com';

        /** @phpstan-var string $supportEmail */
        $supportEmail = Yii::$app->params['supportEmail'] ?? '';

        verify($model->sendEmail(Yii::$app->mailer, $supportEmail, Yii::$app->name))
            ->false(
                "Failed asserting that sendEmail returns 'false' for an inactive user.",
            );
    }

    public function testSendEmailRegeneratesExpiredToken(): void
    {
        /** @phpstan-var User $user */
        $user = User::findByUsername('okirlin');

        // Set an expired token (timestamp far in the past).
        $user->password_reset_token = 'expiredtoken_1000000000';

        $user->save(false);

        $model = new PasswordResetRequestForm();

        $model->email = $user->email;

        /** @phpstan-var string $supportEmail */
        $supportEmail = Yii::$app->params['supportEmail'] ?? '';

        verify($model->sendEmail(Yii::$app->mailer, $supportEmail, Yii::$app->name))
            ->notEmpty(
                'Failed asserting that email is sent after regenerating expired token.',
            );

        $user->refresh();

        verify($user->password_reset_token)
            ->notEquals(
                'expiredtoken_1000000000',
                'Failed asserting that the expired token was replaced with a new one.',
            );
    }

    public function testSendEmailReturnsFalseWhenSaveFails(): void
    {
        /** @phpstan-var User $user */
        $user = User::findByUsername('okirlin');

        // set an expired token so `generatePasswordResetToken()` + `save()` path is triggered.
        $user->password_reset_token = 'expiredtoken_1000000000';

        $user->save(false);

        // force `save()` to fail via `EVENT_BEFORE_SAVE` at the class level.
        $handler = static function (ModelEvent $event): void {
            $event->isValid = false;
        };

        Event::on(User::class, BaseActiveRecord::EVENT_BEFORE_UPDATE, $handler);

        $model = new PasswordResetRequestForm();

        $model->email = $user->email;

        /** @phpstan-var string $supportEmail */
        $supportEmail = Yii::$app->params['supportEmail'] ?? '';

        try {
            verify($model->sendEmail(Yii::$app->mailer, $supportEmail, Yii::$app->name))
                ->false(
                    "Failed asserting that sendEmail returns 'false' when user save fails.",
                );
        } finally {
            Event::off(User::class, BaseActiveRecord::EVENT_BEFORE_UPDATE, $handler);
        }
    }

    public function testSendEmailSuccessfully(): void
    {
        /** @phpstan-var User $user */
        $user = User::findByUsername('okirlin');

        $model = new PasswordResetRequestForm();

        $model->email = $user->email;

        /** @phpstan-var string $supportEmail */
        $supportEmail = Yii::$app->params['supportEmail'] ?? '';

        verify($model->sendEmail(Yii::$app->mailer, $supportEmail, Yii::$app->name))
            ->notEmpty(
                "Failed asserting that 'password reset' email is sent successfully.",
            );
        verify($user->password_reset_token)
            ->notEmpty(
                "Failed asserting that user has a 'password reset token' after sending.",
            );

        /** @phpstan-var MessageInterface|null $emailMessage */
        $emailMessage = $this->tester?->grabLastSentEmail();

        verify($emailMessage)
            ->instanceOf(
                MessageInterface::class,
                'Failed asserting that a reset email was captured.',
            );
        verify($emailMessage?->getTo())
            ->arrayHasKey(
                $model->email,
                'Failed asserting that email is sent to the requested address.',
            );
        verify($emailMessage?->getFrom())
            ->arrayHasKey(
                $supportEmail,
                'Failed asserting that email is sent from the support address.',
            );
    }

    public function testSendMessageWithWrongEmailAddress(): void
    {
        $model = new PasswordResetRequestForm();

        $model->email = 'not-existing-email@example.com';

        /** @phpstan-var string $supportEmail */
        $supportEmail = Yii::$app->params['supportEmail'] ?? '';

        verify($model->sendEmail(Yii::$app->mailer, $supportEmail, Yii::$app->name))
            ->false(
                "Failed asserting that sendEmail returns 'false' for a non-existing email address.",
            );
    }
}
