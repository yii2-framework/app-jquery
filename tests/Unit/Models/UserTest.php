<?php

declare(strict_types=1);

namespace app\tests\Unit\Models;

use app\Models\User;
use app\tests\Support\Fixtures\UserFixture;
use app\tests\Support\UnitTester;
use Yii;
use yii\base\NotSupportedException;

use function strlen;

/**
 * Unit tests for {@see \app\Models\User} ActiveRecord identity model.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
final class UserTest extends \Codeception\Test\Unit
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

    public function testFindIdentityByAccessTokenThrowsException(): void
    {
        $this->tester?->expectThrowable(
            NotSupportedException::class,
            static function (): void {
                User::findIdentityByAccessToken('any-token');
            },
        );
    }

    public function testFindUserById(): void
    {
        $user = User::findIdentity(1);

        verify($user)
            ->notEmpty(
                "Failed asserting that active user with ID '1' exists.",
            );
        verify($user?->username)
            ->equals(
                'okirlin',
                "Failed asserting that user with ID '1' has username 'okirlin'.",
            );
        verify(User::findIdentity(999))
            ->empty(
                "Failed asserting that user with non-existing ID '999' returns 'null'.",
            );
    }

    public function testFindUserByPasswordResetToken(): void
    {
        /** @var User $user */
        $user = User::findByUsername('okirlin');

        verify($user)
            ->notEmpty(
                "Failed asserting that fixture user 'okirlin' exists.",
            );
        verify($user->password_reset_token)
            ->notEmpty(
                "Failed asserting that fixture user 'okirlin' has a password reset token.",
            );

        /** @phpstan-var string $token */
        $token = $user->password_reset_token;

        verify(User::findByPasswordResetToken($token))
            ->notEmpty(
                "Failed asserting that user is found by a valid 'password reset token'.",
            );
        verify(User::findByPasswordResetToken('notexistingtoken_1391882543'))
            ->empty(
                "Failed asserting that an invalid 'password reset token' returns 'null'.",
            );
    }

    public function testFindUserByUsername(): void
    {
        verify(User::findByUsername('okirlin'))
            ->notEmpty(
                "Failed asserting that active user 'okirlin' is found by username.",
            );
        verify(User::findByUsername('not-existing'))
            ->empty(
                "Failed asserting that non-existing username returns 'null'.",
            );
    }

    public function testFindUserByVerificationToken(): void
    {
        verify(User::findByVerificationToken('4ch0qbfhvWwkcuWqjN8SWRq72SOw1KYT_1548675330'))
            ->notEmpty(
                'Failed asserting that inactive user is found by verification token.',
            );
        verify(User::findByVerificationToken('non_existing_token'))
            ->empty(
                "Failed asserting that a non-existing verification token returns 'null'.",
            );
    }

    public function testGenerateAuthKey(): void
    {
        $user = new User();

        $user->generateAuthKey();

        verify($user->auth_key)
            ->notEmpty(
                'Failed asserting that auth key is generated.',
            );
        verify(strlen($user->auth_key))
            ->equals(
                32,
                "Failed asserting that auth key length is '32' characters.",
            );
    }

    public function testGenerateEmailVerificationToken(): void
    {
        $user = new User();

        $user->generateEmailVerificationToken();

        verify($user->verification_token)
            ->notEmpty(
                'Failed asserting that email verification token is generated.',
            );
    }

    public function testGeneratePasswordResetToken(): void
    {
        $user = new User();

        $user->generatePasswordResetToken();

        verify($user->password_reset_token)
            ->notEmpty(
                'Failed asserting that password reset token is generated.',
            );
        verify(User::isPasswordResetTokenValid($user->password_reset_token))
            ->true(
                "Failed asserting that newly generated 'password reset token' is valid.",
            );
    }

    public function testIsPasswordResetTokenValidWithExpiredToken(): void
    {
        /** @phpstan-var int $expire */
        $expire = Yii::$app->params['user.passwordResetTokenExpire'] ?? 3600;

        $expiredToken = 'somevalidvalue_' . (time() - $expire - 1);

        verify(User::isPasswordResetTokenValid($expiredToken))
            ->false(
                'Failed asserting that an expired password reset token is invalid.',
            );
    }

    public function testIsPasswordResetTokenValidWithNullToken(): void
    {
        verify(User::isPasswordResetTokenValid(null))
            ->false(
                "Failed asserting that 'null' token is invalid.",
            );
        verify(User::isPasswordResetTokenValid(''))
            ->false(
                "Failed asserting that empty 'token' is invalid.",
            );
    }

    public function testIsPasswordResetTokenValidWithoutUnderscore(): void
    {
        verify(User::isPasswordResetTokenValid('tokenWithoutUnderscore'))
            ->false('Failed asserting that token without underscore separator is invalid.');
    }

    public function testRemovePasswordResetToken(): void
    {
        /** @var User $user */
        $user = User::findByUsername('okirlin');

        $user->removePasswordResetToken();

        verify($user->password_reset_token)
            ->empty(
                'Failed asserting that password reset token is removed.',
            );
    }

    public function testSetPassword(): void
    {
        $user = new User();

        $user->setPassword('new_password');

        verify($user->password_hash)
            ->notEmpty(
                "Failed asserting that password hash is generated after 'setPassword()'.",
            );
        verify($user->validatePassword('new_password'))
            ->true(
                'Failed asserting that the newly set password validates correctly.',
            );
    }

    public function testValidateAuthKey(): void
    {
        /** @var User $user */
        $user = User::findByUsername('okirlin');

        verify($user->validateAuthKey($user->auth_key))
            ->true('Failed asserting that correct auth key validates successfully.');
        verify($user->validateAuthKey('wrong-auth-key'))
            ->false('Failed asserting that wrong auth key does not validate.');
    }

    public function testValidatePassword(): void
    {
        /** @var User $user */
        $user = User::findByUsername('okirlin');

        verify($user->validatePassword('password_0'))
            ->true(
                'Failed asserting that the correct password validates successfully.',
            );
        verify($user->validatePassword('wrong_password'))
            ->false(
                'Failed asserting that a wrong password does not validate.',
            );
    }
}
