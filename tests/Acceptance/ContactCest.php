<?php

declare(strict_types=1);

namespace app\tests\Acceptance;

use app\tests\Support\AcceptanceTester;
use yii\helpers\Url;

/**
 * Acceptance tests for the contact page.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
final class ContactCest
{
    public function _before(AcceptanceTester $I): void
    {
        $I->amOnPage(Url::toRoute('/site/contact'));
    }

    public function contactFormCanBeSubmitted(AcceptanceTester $I): void
    {
        $I->amGoingTo('submit contact form with correct data');
        $I->fillField('#contactform-name', 'tester');
        $I->fillField('#contactform-email', 'tester@example.com');
        $I->fillField('#contactform-phone', '(555) 123-4567');
        $I->fillField('#contactform-subject', 'test subject');
        $I->fillField('#contactform-body', 'test content');
        $I->fillField('#contactform-verifycode', 'testme');

        $I->click('button[name="contact-button"]');

        $I->dontSeeElement('#contact-form');
        $I->see('Thank you for contacting us. We will respond to you as soon as possible.');
    }

    public function contactPageWorks(AcceptanceTester $I): void
    {
        $I->wantTo('ensure that contact page works');
        $I->see('Contact', 'h1');
    }
}
