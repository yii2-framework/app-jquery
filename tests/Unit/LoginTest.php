<?php

declare(strict_types=1);

namespace app\tests\Unit;

use app\Controllers\SiteController;
use app\Models\User;
use app\tests\Support\Fixtures\UserFixture;
use Yii;
use yii\web\View;

/**
 * Unit tests for {@see \app\Controllers\SiteController} login and about actions.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
final class LoginTest extends \Codeception\Test\Unit
{
    /**
     * @phpstan-return array{user: array{class: string, dataFile: string}}
     */
    public function _fixtures(): array
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                // @phpstan-ignore binaryOp.invalid
                'dataFile' => codecept_data_dir() . 'user.php',
            ],
        ];
    }

    public function testActionAboutRendersPage(): void
    {
        $controller = new SiteController(
            'site',
            Yii::$app,
            Yii::$app->mailer,
        );

        Yii::$app->controller = $controller;

        $output = $controller->actionAbout();

        self::assertStringContainsString(
            'About',
            $output,
            'Failed asserting that about page renders content with "About" text.',
        );
    }

    public function testActionLoginRedirectsWhenAlreadyLoggedIn(): void
    {
        $controller = new SiteController(
            'site',
            Yii::$app,
            Yii::$app->mailer,
        );

        $view = new View(['context' => $controller]);

        $user = User::findIdentity(1);

        self::assertInstanceOf(
            User::class,
            $user,
            "Failed asserting that fixture user with ID '1' exists.",
        );

        Yii::$app->user->login($user);

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
