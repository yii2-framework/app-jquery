<?php

declare(strict_types=1);

namespace app\tests\Unit\Models;

use app\Models\User;
use app\Models\VerifyEmailForm;
use app\tests\Support\Fixtures\UserFixture;
use app\tests\Support\UnitTester;
use yii\base\InvalidArgumentException;

/**
 * Unit tests for {@see \app\Models\VerifyEmailForm} model.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
final class VerifyEmailFormTest extends \Codeception\Test\Unit
{
    protected UnitTester|null $tester = null;

    public function _before(): void
    {
        $this->tester?->haveFixtures(
            [
                'user' => [
                    'class' => UserFixture::class,
                    // @phpstan-ignore binaryOp.invalid
                    'dataFile' => codecept_data_dir() . 'user.php',
                ],
            ],
        );
    }

    public function testAlreadyActivatedToken(): void
    {
        /** @phpstan-var User $user */
        $user = User::findOne(['username' => 'test2.test']);

        $token = $user->verification_token ?? '';

        $this->tester?->expectThrowable(
            InvalidArgumentException::class,
            static function () use ($token): void {
                new VerifyEmailForm($token);
            },
        );
    }

    public function testVerifyCorrectToken(): void
    {
        /** @phpstan-var User $user */
        $user = User::findOne(['username' => 'test.test']);

        $model = new VerifyEmailForm($user->verification_token ?? '');

        $user = $model->verifyEmail();

        verify($user)
            ->instanceOf(
                User::class,
                "Failed asserting that 'verifyEmail()' returns a User instance.",
            );
        verify($user?->username)
            ->equals(
                'test.test',
                "Failed asserting that verified user has username 'test.test'.",
            );
        verify($user?->email)
            ->equals(
                'test@mail.com',
                "Failed asserting that verified user has email 'test@mail.com'.",
            );
        verify($user?->status)
            ->equals(
                User::STATUS_ACTIVE,
                'Failed asserting that verified user status is ACTIVE.',
            );
        verify($user?->validatePassword('Test1234'))
            ->true(
                "Failed asserting that verified 'user password' still validates correctly.",
            );
    }

    public function testVerifyWrongToken(): void
    {
        $this->tester?->expectThrowable(
            InvalidArgumentException::class,
            static function (): void {
                new VerifyEmailForm('');
            },
        );

        $this->tester?->expectThrowable(
            InvalidArgumentException::class,
            static function (): void {
                new VerifyEmailForm('notexistingtoken_1391882543');
            },
        );
    }
}
