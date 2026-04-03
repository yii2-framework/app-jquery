<?php

declare(strict_types=1);

namespace app\tests\unit\migrations;

use app\migrations\M260403000000CreateAdminUser;
use app\models\User;
use Yii;

/**
 * Unit tests for {@see M260403000000CreateAdminUser} migration.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
final class CreateAdminUserTest extends \Codeception\Test\Unit
{
    public function testSafeDownDeletesAdminUser(): void
    {
        $db = Yii::$app->db;
        $migration = new M260403000000CreateAdminUser(['db' => $db]);

        $migration->up();

        $admin = User::find()->where(['username' => 'admin'])->one();

        self::assertNotNull($admin, "Failed asserting that admin user exists after 'safeUp'.");

        $migration->down();

        $admin = User::find()->where(['username' => 'admin'])->one();

        self::assertNull($admin, "Failed asserting that admin user is deleted after 'safeDown'.");
    }

    public function testSafeUpCreatesAdminUser(): void
    {
        $db = Yii::$app->db;

        // clean up if admin already exists from fixtures.
        $db->createCommand()->delete('{{%user}}', ['username' => 'admin'])->execute();

        $migration = new M260403000000CreateAdminUser(['db' => $db]);

        $migration->up();

        $admin = User::find()->where(['username' => 'admin'])->one();

        self::assertNotNull($admin, "Failed asserting that admin user exists after 'safeUp'.");
        self::assertSame('admin', $admin->username);
        self::assertSame('admin@example.com', $admin->email);
        self::assertSame(User::STATUS_ACTIVE, $admin->status);
        self::assertTrue(
            Yii::$app->security->validatePassword('admin', $admin->password_hash),
            "Failed asserting that admin password is 'admin'.",
        );

        // clean up for other tests.
        $migration->down();
    }
}
