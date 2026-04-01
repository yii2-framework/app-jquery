<?php

declare(strict_types=1);

namespace app\tests\unit;

use app\controllers\SiteController;
use app\models\User;
use app\tests\support\Fixtures\UserFixture;
use Yii;
use yii\web\IdentityInterface;
use yii\web\View;

/**
 * Unit tests for {@see \app\controllers\SiteController} logout action and layout rendering.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
final class LogoutTest extends \Codeception\Test\Unit
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

    public function testRenderLogoutLinkWhenUserIsLoggedIn(): void
    {
        $user = User::findIdentity(1);

        $controller = new SiteController(
            'site',
            Yii::$app,
            Yii::$app->mailer,
        );

        $view = new View(['context' => $controller]);

        self::assertNotNull(
            $user,
            "Failed asserting that the user identity with ID '1' exists.",
        );
        self::assertInstanceOf(
            IdentityInterface::class,
            $user,
            "Failed asserting that the identity is an instance of 'Identity' class.",
        );

        Yii::$app->user->login($user);

        $html = $view->render('//layouts/main.php', ['content' => 'Hello World']);

        self::assertStringContainsString(
            'Logout (okirlin)',
            $html,
            'Failed asserting that the logout link is rendered for a logged-in user.',
        );
        self::assertStringContainsString(
            'data-method="post"',
            $html,
            'Failed asserting that the logout link uses POST method.',
        );

        $controller->actionLogout();

        $html = $view->render('//layouts/main.php', ['content' => 'Hello World']);

        self::assertStringNotContainsString(
            'Logout (',
            $html,
            'Failed asserting that the logout link is not rendered after logout.',
        );
    }
}
