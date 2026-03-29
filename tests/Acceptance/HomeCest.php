<?php

declare(strict_types=1);

namespace yii\demo\basic\tests\Acceptance;

use yii\demo\basic\tests\Support\AcceptanceTester;
use yii\helpers\Url;

final class HomeCest
{
    public function ensureThatExtensionGridIsRendered(AcceptanceTester $I): void
    {
        $I->amOnPage(Url::toRoute('/site/index'));

        $I->seeElement('.extension-card');
        $I->see('yii2-debug');
        $I->seeLink('Learn more »');
    }
    public function ensureThatHomePageWorks(AcceptanceTester $I): void
    {
        $I->amOnPage(Url::toRoute('/site/index'));
        $I->see(\Yii::$app->name);

        $I->seeLink('About');
        $I->click('About');

        $I->see('This is the About page.');
    }
}
