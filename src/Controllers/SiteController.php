<?php

declare(strict_types=1);

namespace app\Controllers;

use app\Models\ContactForm;
use app\Models\LoginForm;
use app\Models\PasswordResetRequestForm;
use app\Models\ResendVerificationEmailForm;
use app\Models\ResetPasswordForm;
use app\Models\SignupForm;
use app\Models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\captcha\CaptchaAction;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\mail\MailerInterface;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;

/**
 * Handles site pages: home, about, contact, login, logout, signup, and password recovery actions.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
class SiteController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly MailerInterface $mailer,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * Displays about page.
     */
    public function actionAbout(): string
    {
        return $this->render('about');
    }

    /**
     * Displays contact page.
     */
    public function actionContact(): Response|string
    {
        $model = new ContactForm();

        /** @phpstan-var array<string, mixed> $post */
        $post = $this->request->post();

        /** @phpstan-var array{adminEmail: string, senderEmail: string, senderName: string} $params */
        $params = Yii::$app->params;

        $contact = $model->load($post) && $model->contact(
            $this->mailer,
            $params['adminEmail'],
            $params['senderEmail'],
            $params['senderName'],
        );

        if ($contact) {
            Yii::$app->session->setFlash(
                'success',
                'Thank you for contacting us. We will respond to you as soon as possible.',
            );

            return $this->refresh();
        }

        return $this->render('contact', ['model' => $model]);
    }

    /**
     * Displays homepage.
     */
    public function actionIndex(): string
    {
        return $this->render('index');
    }

    /**
     * Login action.
     */
    public function actionLogin(): Response|string
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        /** @phpstan-var array<string, mixed> $post */
        $post = $this->request->post();

        if ($model->load($post) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', ['model' => $model]);
    }

    /**
     * Logout action.
     */
    public function actionLogout(): Response
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Requests password reset.
     */
    public function actionRequestPasswordReset(): Response|string
    {
        $model = new PasswordResetRequestForm();

        /** @phpstan-var array<string, mixed> $post */
        $post = $this->request->post();

        /** @phpstan-var array{supportEmail: string} $params */
        $params = Yii::$app->params;

        if ($model->load($post) && $model->validate()) {
            $sent = $model->sendEmail(
                $this->mailer,
                $params['supportEmail'],
                Yii::$app->name,
            );

            if ($sent) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            }

            Yii::$app->session->setFlash(
                'error',
                'Sorry, we are unable to reset password for the provided email address.',
            );
        }

        return $this->render('requestPasswordResetToken', ['model' => $model]);
    }

    /**
     * Resends verification email.
     */
    public function actionResendVerificationEmail(): Response|string
    {
        $model = new ResendVerificationEmailForm();

        /** @phpstan-var array<string, mixed> $post */
        $post = $this->request->post();

        /** @phpstan-var array{supportEmail: string} $params */
        $params = Yii::$app->params;

        if ($model->load($post) && $model->validate()) {
            $sent = $model->sendEmail(
                $this->mailer,
                $params['supportEmail'],
                Yii::$app->name,
            );

            if ($sent) {
                Yii::$app->session->setFlash(
                    'success',
                    'Check your email for further instructions.',
                );

                return $this->goHome();
            }

            Yii::$app->session->setFlash(
                'error',
                'Sorry, we are unable to resend verification email for the provided email address.',
            );
        }

        return $this->render('resendVerificationEmail', ['model' => $model]);
    }

    /**
     * Resets password.
     *
     * @throws BadRequestHttpException
     */
    public function actionResetPassword(string $token): Response|string
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        /** @phpstan-var array<string, mixed> $post */
        $post = $this->request->post();

        if ($model->load($post) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash(
                'success',
                'New password saved.',
            );

            return $this->goHome();
        }

        return $this->render('resetPassword', ['model' => $model]);
    }

    public function actions(): array
    {
        return [
            'captcha' => [
                'class' => CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'transparent' => true,
            ],
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    /**
     * Signs user up.
     */
    public function actionSignup(): Response|string
    {
        $model = new SignupForm();

        /** @phpstan-var array<string, mixed> $post */
        $post = $this->request->post();

        /** @phpstan-var array{supportEmail: string} $params */
        $params = Yii::$app->params;

        $signed = $model->load($post) && $model->signup(
            $this->mailer,
            $params['supportEmail'],
            Yii::$app->name,
        ) === true;

        if ($signed) {
            Yii::$app->session->setFlash(
                'success',
                'Thank you for registration. Please check your inbox for verification email.',
            );

            return $this->goHome();
        }

        return $this->render('signup', ['model' => $model]);
    }

    /**
     * Verifies email address.
     *
     * @throws BadRequestHttpException
     */
    public function actionVerifyEmail(string $token): Response
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->verifyEmail() !== null) {
            Yii::$app->session->setFlash(
                'success',
                'Your email has been confirmed!',
            );

            return $this->goHome();
        }

        Yii::$app->session->setFlash(
            'error',
            'Sorry, we are unable to verify your account with provided token.',
        );

        return $this->goHome();
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => [
                    'logout',
                    'signup',
                    'request-password-reset',
                    'resend-verification-email',
                ],
                'rules' => [
                    [
                        'actions' => [
                            'signup',
                            'request-password-reset',
                            'resend-verification-email',
                        ],
                        'allow' => true,
                        'roles' => [
                            '?',
                        ],
                    ],
                    [
                        'actions' => [
                            'logout',
                        ],
                        'allow' => true,
                        'roles' => [
                            '@',
                        ],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => [
                        'post',
                    ],
                ],
            ],
        ];
    }
}
