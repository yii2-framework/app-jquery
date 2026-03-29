<?php

declare(strict_types=1);

namespace yii\demo\basic\Models;

use yii\base\BaseObject;
use yii\web\IdentityInterface;

/**
 * Provides a static-data identity implementation for authentication.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
class User extends BaseObject implements IdentityInterface
{
    public string $accessToken = '';
    public string $authKey = '';
    public int|string $id = '';
    public string $passwordHash = '';
    public string $username = '';

    /**
     * @phpstan-var array<
     *   int,
     *   array{id: string, username: string, passwordHash: string, authKey: string, accessToken: string},
     * >
     */
    private static array $users = [
        100 => [
            'id' => '100',
            'username' => 'admin',
            // password: admin
            'passwordHash' => '$2y$13$gYAywKSkhfZDq9FLNdm7buKnvlRxDexf5xipSMAxQPDUxpaptmZJu',
            'authKey' => 'test100key',
            'accessToken' => '100-token',
        ],
        101 => [
            'id' => '101',
            'username' => 'demo',
            // password: demo
            'passwordHash' => '$2y$13$alRLq1PGVMlGYwS/Y3iy3ewQns1Z8ol8Iq6Zb5k7ZwEhblA1aL29y',
            'authKey' => 'test101key',
            'accessToken' => '101-token',
        ],
    ];

    /**
     * Finds user by username.
     */
    public static function findByUsername(string $username): self|null
    {
        foreach (self::$users as $user) {
            if (strcasecmp($user['username'], $username) === 0) {
                return new self($user);
            }
        }

        return null;
    }

    public static function findIdentity($id): self|null
    {
        return isset(self::$users[$id]) ? new self(self::$users[$id]) : null;
    }

    public static function findIdentityByAccessToken($token, $type = null): self|null
    {
        foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new self($user);
            }
        }

        return null;
    }

    public function getAuthKey(): string
    {
        return $this->authKey;
    }

    public function getId(): int|string
    {
        return $this->id;
    }

    public function validateAuthKey($authKey): bool
    {
        return $this->authKey === $authKey;
    }
}
