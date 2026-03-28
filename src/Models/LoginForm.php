<?php

declare(strict_types=1);

namespace yii\demo\basic\Models;

use Yii;
use yii\base\Model;
use yii\base\Security;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user
 */
class LoginForm extends Model
{
    public string $password = '';
    public bool $rememberMe = true;
    public string $username = '';

    private User|null $user = null;
    private bool $userLoaded = false;
    private string $userLookup = '';

    /**
     * @param Security $security the security component.
     * @param array<string, mixed> $config name-value pairs that will be used to initialize the object properties.
     */
    public function __construct(private readonly Security $security, array $config = [])
    {
        parent::__construct($config);
    }

    /**
     * Finds user by [[username]].
     */
    public function getUser(): User|null
    {
        if (!$this->userLoaded || $this->userLookup !== $this->username) {
            $this->user = User::findByUsername($this->username);
            $this->userLoaded = true;
            $this->userLookup = $this->username;
        }

        return $this->user;
    }

    /**
     * Logs in a user using the provided username and password.
     */
    public function login(): bool
    {
        if ($this->validate()) {
            /** @var User $user */
            $user = $this->getUser();

            return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
        }

        return false;
    }

    /**
     * @return array<array<mixed>> the validation rules.
     */
    public function rules(): array
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     */
    public function validatePassword(string $attribute, mixed $params): void
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if ($user === null || !$this->security->validatePassword($this->password, $user->passwordHash)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }
}
