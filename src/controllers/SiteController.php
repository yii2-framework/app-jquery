<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\ContactForm;
use Yii;
use yii\captcha\CaptchaAction;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\mail\MailerInterface;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;

/**
 * Handles site pages: home, about, contact, and error actions.
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

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => [],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [
                            '?',
                            '@',
                        ],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [],
            ],
        ];
    }
}
