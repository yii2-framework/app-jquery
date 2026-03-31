<?php

declare(strict_types=1);

namespace app\tests\Support\Helper;

use Codeception\Module;

/**
 * Provides custom assertion helpers for functional tests.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
final class Functional extends Module
{
    public function dontSeeValidationError(string $message): void
    {
        $this->getModule('Yii2')->dontSee($message, '.invalid-feedback');
    }
    public function seeValidationError(string $message): void
    {
        $this->getModule('Yii2')->see($message, '.invalid-feedback');
    }
}
