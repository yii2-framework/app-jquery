<?php

declare(strict_types=1);

namespace app\tests\Unit;

use app\Controllers\SiteController;
use app\Models\User;
use Yii;
use yii\base\Security;
use yii\web\View;

/**
 * Unit tests for {@see \app\Controllers\SiteController} login and about actions.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
final class LoginTest extends \Codeception\Test\Unit
{
    public function testActionAboutRendersPage(): void
    {
        $controller = new SiteController(
            'site',
            Yii::$app,
            Yii::$app->mailer,
            new Security(),
        );

        Yii::$app->controller = $controller;

        $output = $controller->actionAbout();

        self::assertStringContainsString('About', $output);
    }

    public function testActionLoginRedirectsWhenAlreadyLoggedIn(): void
    {
        $controller = new SiteController(
            'site',
            Yii::$app,
            Yii::$app->mailer,
            new Security(),
        );

        $view = new View(['context' => $controller]);

        Yii::$app->user->login(new User());

        $controller->actionLogin();

        self::assertStringNotContainsString(
            'Logout (admin)',
            $view->render('//layouts/main.php', ['content' => 'Hello World']),
            'Failed asserting that the logout link is not rendered for a wrong username.',
        );
    }

    public function testRenderLoginForGuest(): void
    {
        $controller = new SiteController(
            'site',
            Yii::$app,
            Yii::$app->mailer,
            new Security(),
        );

        $view = new View(['context' => $controller]);

        Yii::$app->user->logout();

        $output = $view->render('//layouts/main.php', ['content' => 'Hello World']);

        self::assertStringContainsString(
            'Login',
            $output,
            'Failed asserting that the login link is rendered for guests.',
        );
        self::assertStringNotContainsString(
            'Logout (',
            $output,
            'Failed asserting that the logout link is not rendered for guests.',
        );
    }
}
