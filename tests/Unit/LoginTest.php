<?php

declare(strict_types=1);

namespace yii\demo\basic\tests\Unit;

use Yii;
use yii\base\Security;
use yii\demo\basic\Controllers\SiteController;
use yii\web\View;

final class LoginTest extends \Codeception\Test\Unit
{
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
