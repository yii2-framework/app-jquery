<?php

declare(strict_types=1);

namespace yii\demo\basic\tests\Unit;

use Yii;
use yii\base\Security;
use yii\demo\basic\Controllers\SiteController;
use yii\demo\basic\Models\User;
use yii\web\IdentityInterface;
use yii\web\View;

final class LogoutTest extends \Codeception\Test\Unit
{
    public function testRenderLogoutLinkWhenUserIsLoggedIn(): void
    {
        $user = User::findIdentity('100');

        $controller = new SiteController(
            'site',
            Yii::$app,
            Yii::$app->mailer,
            new Security(),
        );

        $view = new View(['context' => $controller]);

        self::assertNotNull(
            $user,
            "Failed asserting that the user identity with ID '100' exists.",
        );
        self::assertInstanceOf(
            IdentityInterface::class,
            $user,
            "Failed asserting that the identity is an instance of 'Identity' class.",
        );

        Yii::$app->user->login($user);

        try {
            $html = $view->render('//layouts/main.php', ['content' => 'Hello World']);

            self::assertStringContainsString(
                'Logout (admin)',
                $html,
                'Failed asserting that the logout link is rendered for a logged-in user.',
            );
            self::assertStringContainsString(
                'data-method="post"',
                $html,
                'Failed asserting that the logout link uses POST method.',
            );
        } finally {
            Yii::$app->user->logout();
        }

        $html = $view->render('//layouts/main.php', ['content' => 'Hello World']);

        self::assertStringNotContainsString(
            'Logout (',
            $html,
            'Failed asserting that the logout link is not rendered after logout.',
        );
    }
}
