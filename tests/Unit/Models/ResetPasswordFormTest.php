<?php

declare(strict_types=1);

namespace app\tests\Unit\Models;

use app\Models\ResetPasswordForm;
use app\Models\User;
use app\tests\Support\Fixtures\UserFixture;
use app\tests\Support\UnitTester;
use yii\base\InvalidArgumentException;

/**
 * Unit tests for {@see \app\Models\ResetPasswordForm} model.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
final class ResetPasswordFormTest extends \Codeception\Test\Unit
{
    protected UnitTester|null $tester = null;

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

    public function testResetCorrectToken(): void
    {
        /** @phpstan-var User $user */
        $user = User::findByUsername('okirlin');

        verify($user)
            ->notEmpty(
                "Failed asserting that fixture user 'okirlin' exists.",
            );
        verify($user->password_reset_token)
            ->notEmpty(
                "Failed asserting that fixture user 'okirlin' has a 'password reset token'.",
            );

        /** @phpstan-var string $token */
        $token = $user->password_reset_token;

        $form = new ResetPasswordForm($token);

        $form->password = 'new_password_123';

        verify($form->resetPassword())
            ->notEmpty(
                'Failed asserting that password reset succeeds with a valid token and password.',
            );

        $user->refresh();

        verify($user->validatePassword('new_password_123'))
            ->true(
                'Failed asserting that the new password validates after reset.',
            );
    }

    public function testResetWrongToken(): void
    {
        $this->tester?->expectThrowable(
            InvalidArgumentException::class,
            static function (): void {
                new ResetPasswordForm('');
            },
        );

        $this->tester?->expectThrowable(
            InvalidArgumentException::class,
            static function (): void {
                new ResetPasswordForm('notexistingtoken_1391882543');
            },
        );
    }
}
