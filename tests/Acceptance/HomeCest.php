<?php

declare(strict_types=1);

namespace yii\demo\basic\tests\Acceptance;

use yii\demo\basic\tests\Support\AcceptanceTester;
use yii\helpers\Url;

final class HomeCest
{
    public function ensureThatHomePageWorks(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute('/site/index'));
        $I->see(\Yii::$app->name);

        $I->seeLink('About');
        $I->click('About');

        $I->see('This is the About page.');
    }
}
