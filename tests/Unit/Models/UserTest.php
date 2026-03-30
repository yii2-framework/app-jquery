<?php

declare(strict_types=1);

namespace app\tests\Unit\Models;

use app\Models\User;

/**
 * Unit tests for {@see \app\Models\User} identity model.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
final class UserTest extends \Codeception\Test\Unit
{
    public function testFindUserByAccessToken(): void
    {
        /** @var User $user */
        $user = User::findIdentityByAccessToken('100-token');

        verify($user)->notEmpty();
        verify($user->username)->equals('admin');
        verify(User::findIdentityByAccessToken('non-existing'))->empty();
    }

    public function testFindUserById(): void
    {
        /** @var User $user */
        $user = User::findIdentity(100);

        verify($user)->notEmpty();
        verify($user->username)->equals('admin');
        verify(User::findIdentity(999))->empty();
    }

    public function testFindUserByUsername(): void
    {
        /** @var User $user */
        $user = User::findByUsername('admin');

        verify($user)->notEmpty();
        verify(User::findByUsername('not-admin'))->empty();
    }

    /**
     * @depends testFindUserByUsername
     */
    public function testValidateUser(): void
    {
        /** @var User $user */
        $user = User::findByUsername('admin');

        verify($user->validateAuthKey('test100key'))->true();
        verify($user->validateAuthKey('test102key'))->false();
    }
}
