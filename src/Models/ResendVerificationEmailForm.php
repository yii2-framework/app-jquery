<?php

declare(strict_types=1);

namespace app\Models;

use yii\base\Model;
use yii\mail\MailerInterface;

/**
 * Handles resending of verification email to inactive users.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
class ResendVerificationEmailForm extends Model
{
    public string $email = '';

    public function rules(): array
    {
        return [
            [
                'email',
                'trim',
            ],
            [
                'email',
                'required',
            ],
            [
                'email',
                'email',
            ],
            [
                'email',
                'exist',
                'targetClass' => User::class,
                'filter' => ['status' => User::STATUS_INACTIVE],
                'message' => 'There is no user with this email address.',
            ],
        ];
    }

    /**
     * Sends confirmation email to user.
     */
    public function sendEmail(MailerInterface $mailer, string $supportEmail, string $appName): bool
    {
        $user = User::findOne(
            [
                'email' => $this->email,
                'status' => User::STATUS_INACTIVE,
            ],
        );

        if ($user === null) {
            return false;
        }

        return $mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user],
            )
            ->setFrom([$supportEmail => $appName . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . $appName)
            ->send();
    }
}
