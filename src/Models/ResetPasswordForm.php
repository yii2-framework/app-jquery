<?php

declare(strict_types=1);

namespace app\Models;

use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;

/**
 * Handles password reset with a valid token.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
class ResetPasswordForm extends Model
{
    public string $password = '';

    private User|null $_user = null;

    /**
     * Creates a form model given a token.
     *
     * @param string $token the password reset token.
     * @param array $config name-value pairs that will be used to initialize the object properties.
     *
     * @throws InvalidArgumentException if token is empty or not valid.
     *
     * @phpstan-param array<string, mixed> $config
     */
    public function __construct(string $token, array $config = [])
    {
        if ($token === '') {
            throw new InvalidArgumentException('Password reset token cannot be blank.');
        }

        $this->_user = User::findByPasswordResetToken($token);

        if ($this->_user === null) {
            throw new InvalidArgumentException('Wrong password reset token.');
        }

        parent::__construct($config);
    }

    /**
     * Resets password.
     */
    public function resetPassword(): bool
    {
        /** @var User $user */
        $user = $this->_user;

        $user->setPassword($this->password);
        $user->removePasswordResetToken();
        $user->generateAuthKey();

        return $user->save(false);
    }

    public function rules(): array
    {
        return [
            [
                'password',
                'required',
            ],
            [
                'password',
                'string',
                'min' => Yii::$app->params['user.passwordMinLength'] ?? 8,
            ],
        ];
    }
}
