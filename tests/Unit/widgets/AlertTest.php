<?php

declare(strict_types=1);

namespace yii\demo\basic\tests\Unit\widgets;

use Yii;
use yii\demo\basic\Widgets\Alert;

class AlertTest extends \Codeception\Test\Unit
{
    /**
     * @var array<string, array{string, string, string[]}>
     */
    private const ALERT_TYPE_CASES = [
        'danger' => ['danger', 'alert-danger', ['alert-success', 'alert-info', 'alert-warning']],
        'error' => ['error', 'alert-danger', ['alert-success', 'alert-info', 'alert-warning']],
        'info' => ['info', 'alert-info', ['alert-danger', 'alert-success', 'alert-warning']],
        'success' => ['success', 'alert-success', ['alert-danger', 'alert-info', 'alert-warning']],
        'warning' => ['warning', 'alert-warning', ['alert-danger', 'alert-success', 'alert-info']],
    ];

    /**
     * @return array<string, array{string, string, string[]}>
     */
    public static function multipleMessagesProvider(): array
    {
        return self::ALERT_TYPE_CASES;
    }

    /**
     * @return array<string, array{string, string, string[]}>
     */
    public static function singleMessageProvider(): array
    {
        return self::ALERT_TYPE_CASES;
    }

    public function testFlashIntegrity(): void
    {
        $errorMessage = 'This is an error message';
        $unrelatedMessage = 'This is a message that is not related to the alert widget';

        Yii::$app->session->setFlash('error', $errorMessage);
        Yii::$app->session->setFlash('unrelated', $unrelatedMessage);

        Alert::widget();

        // Simulate redirect
        Yii::$app->session->close();
        Yii::$app->session->open();

        verify(Yii::$app->session->getFlash('error'))->empty();
        verify(Yii::$app->session->getFlash('unrelated'))->equals($unrelatedMessage);
    }

    /**
     * @dataProvider multipleMessagesProvider
     *
     * @param string[] $excludedClasses
     */
    public function testMultipleMessages(string $flashType, string $expectedClass, array $excludedClasses): void
    {
        $firstMessage = "This is the first {$flashType} message";
        $secondMessage = "This is the second {$flashType} message";

        Yii::$app->session->setFlash($flashType, [$firstMessage, $secondMessage]);

        $renderingResult = Alert::widget();

        verify($renderingResult)->stringContainsString($firstMessage);
        verify($renderingResult)->stringContainsString($secondMessage);
        verify($renderingResult)->stringContainsString($expectedClass);

        foreach ($excludedClasses as $excludedClass) {
            verify($renderingResult)->stringNotContainsString($excludedClass);
        }
    }

    public function testMultipleMixedMessages(): void
    {
        $types = ['error', 'danger', 'success', 'info', 'warning'];
        $messages = [];

        foreach ($types as $type) {
            $messages[$type] = [
                "This is the first {$type} message",
                "This is the second {$type} message",
            ];
            Yii::$app->session->setFlash($type, $messages[$type]);
        }

        $renderingResult = Alert::widget();

        foreach ($messages as $typeMessages) {
            foreach ($typeMessages as $message) {
                verify($renderingResult)->stringContainsString($message);
            }
        }

        verify($renderingResult)->stringContainsString('alert-danger');
        verify($renderingResult)->stringContainsString('alert-success');
        verify($renderingResult)->stringContainsString('alert-info');
        verify($renderingResult)->stringContainsString('alert-warning');
    }
    /**
     * @dataProvider singleMessageProvider
     *
     * @param string[] $excludedClasses
     */
    public function testSingleMessage(string $flashType, string $expectedClass, array $excludedClasses): void
    {
        $message = "This is a {$flashType} message";

        Yii::$app->session->setFlash($flashType, $message);

        $renderingResult = Alert::widget();

        verify($renderingResult)->stringContainsString($message);
        verify($renderingResult)->stringContainsString($expectedClass);

        foreach ($excludedClasses as $excludedClass) {
            verify($renderingResult)->stringNotContainsString($excludedClass);
        }
    }

    public function testSingleMixedMessages(): void
    {
        $errorMessage = 'This is an error message';
        $dangerMessage = 'This is a danger message';
        $successMessage = 'This is a success message';
        $infoMessage = 'This is an info message';
        $warningMessage = 'This is a warning message';

        Yii::$app->session->setFlash('error', $errorMessage);
        Yii::$app->session->setFlash('danger', $dangerMessage);
        Yii::$app->session->setFlash('success', $successMessage);
        Yii::$app->session->setFlash('info', $infoMessage);
        Yii::$app->session->setFlash('warning', $warningMessage);

        $renderingResult = Alert::widget();

        verify($renderingResult)->stringContainsString($errorMessage);
        verify($renderingResult)->stringContainsString($dangerMessage);
        verify($renderingResult)->stringContainsString($successMessage);
        verify($renderingResult)->stringContainsString($infoMessage);
        verify($renderingResult)->stringContainsString($warningMessage);

        verify($renderingResult)->stringContainsString('alert-danger');
        verify($renderingResult)->stringContainsString('alert-success');
        verify($renderingResult)->stringContainsString('alert-info');
        verify($renderingResult)->stringContainsString('alert-warning');
    }

    public function testSkipsSessionStartWhenNoSessionExists(): void
    {
        $session = Yii::$app->session;
        $cookieName = $session->getName();
        $previousCookie = $_COOKIE[$cookieName] ?? null;

        try {
            $session->close();
            unset($_COOKIE[$cookieName]);

            verify($session->getIsActive())->false();
            verify($session->getHasSessionId())->false();

            $renderingResult = Alert::widget();

            verify($renderingResult)->equals('');
            verify($session->getIsActive())->false();
        } finally {
            if ($previousCookie === null) {
                unset($_COOKIE[$cookieName]);
            } else {
                $_COOKIE[$cookieName] = $previousCookie;
            }
        }
    }
}
