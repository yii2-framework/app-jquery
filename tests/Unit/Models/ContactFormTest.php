<?php

declare(strict_types=1);

namespace yii\demo\basic\tests\Unit\Models;

use yii\demo\basic\Models\ContactForm;
use yii\demo\basic\tests\Support\UnitTester;
use yii\mail\MessageInterface;

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
            )
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
