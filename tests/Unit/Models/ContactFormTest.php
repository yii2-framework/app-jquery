<?php

declare(strict_types=1);

namespace app\tests\Unit\Models;

use app\Models\ContactForm;
use app\tests\Support\UnitTester;
use yii\mail\MessageInterface;

/**
 * Unit tests for {@see \app\Models\ContactForm} model.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
final class ContactFormTest extends \Codeception\Test\Unit
{
    public UnitTester|null $tester = null;

    public function testEmailIsSentOnContact(): void
    {
        $model = new ContactForm();

        $model->attributes = [
            'name' => 'Tester',
            'email' => 'tester@example.com',
            'phone' => '(555) 123-4567',
            'subject' => 'very important letter subject',
            'body' => 'body of current message',
            'verifyCode' => 'testme',
        ];

        verify(
            $model->contact(
                \Yii::$app->mailer,
                'admin@example.com',
                'noreply@example.com',
                'Example.com mailer',
            ),
        )->notEmpty();

        // using Yii2 module actions to check email was sent
        $this->tester?->seeEmailIsSent();

        /** @var MessageInterface $emailMessage */
        $emailMessage = $this->tester?->grabLastSentEmail();

        verify($emailMessage)->instanceOf(MessageInterface::class);
        verify($emailMessage->getTo())->arrayHasKey('admin@example.com');
        verify($emailMessage->getFrom())->arrayHasKey('noreply@example.com');
        verify($emailMessage->getReplyTo())->arrayHasKey('tester@example.com');
        verify($emailMessage->getSubject())->equals('very important letter subject');

        /** @phpstan-var \yii\symfonymailer\Message $emailMessage */
        verify($emailMessage->getSymfonyEmail()->getTextBody())->stringContainsString('body of current message');
    }
}
