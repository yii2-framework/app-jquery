<?php

declare(strict_types=1);

namespace yii\demo\basic\tests\Unit\Models;

use yii\demo\basic\Models\User;

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
