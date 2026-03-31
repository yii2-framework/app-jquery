<?php

declare(strict_types=1);

/** @var \app\Models\User $user */
/** @var \yii\web\View $this */

$verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $user->verification_token]);
?>
Hello <?= $user->username ?>,

Follow the link below to verify your email:

<?= $verifyLink ?>
