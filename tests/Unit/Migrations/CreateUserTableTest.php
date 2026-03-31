<?php

declare(strict_types=1);

namespace app\tests\Unit\Migrations;

use app\Migrations\M260330000000CreateUserTable;
use Yii;

/**
 * Unit tests for {@see \app\Migrations\M260330000000CreateUserTable} migration.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
final class CreateUserTableTest extends \Codeception\Test\Unit
{
    public function testSafeDownDropsUserTable(): void
    {
        $db = Yii::$app->db;
        $schema = $db->schema;

        // Table exists before migration down.
        $schema->refresh();

        verify($schema->getTableSchema('{{%user}}'))
            ->notNull('Failed asserting that user table exists before safeDown.');

        $migration = new M260330000000CreateUserTable(['db' => $db]);

        $migration->down();

        $schema->refresh();

        verify($schema->getTableSchema('{{%user}}'))
            ->null('Failed asserting that user table is dropped after safeDown.');

        // Recreate the table for subsequent tests.
        $migration->up();

        $schema->refresh();

        verify($schema->getTableSchema('{{%user}}'))
            ->notNull('Failed asserting that user table is recreated after safeUp.');
    }

    public function testSafeUpCreatesUserTable(): void
    {
        $db = Yii::$app->db;
        $schema = $db->schema;

        $schema->refresh();

        $tableSchema = $schema->getTableSchema('{{%user}}');

        verify($tableSchema)
            ->notNull('Failed asserting that user table exists after safeUp.');

        $columns = $tableSchema->columns ?? [] ;

        verify($columns)
            ->arrayHasKey(
                'id',
                "Failed asserting that 'id' column exists.",
            );
        verify($columns)
            ->arrayHasKey(
                'username',
                "Failed asserting that 'username' column exists.",
            );
        verify($columns)
            ->arrayHasKey(
                'auth_key',
                "Failed asserting that 'auth_key' column exists.",
            );
        verify($columns)
            ->arrayHasKey(
                'password_hash',
                "Failed asserting that 'password_hash' column exists.",
            );
        verify($columns)
            ->arrayHasKey(
                'password_reset_token',
                "Failed asserting that 'password_reset_token' column exists.",
            );
        verify($columns)
            ->arrayHasKey(
                'email',
                "Failed asserting that 'email' column exists.",
            );
        verify($columns)
            ->arrayHasKey(
                'status',
                "Failed asserting that 'status' column exists.",
            );
        verify($columns)
            ->arrayHasKey(
                'created_at',
                "Failed asserting that 'created_at' column exists.",
            );
        verify($columns)
            ->arrayHasKey(
                'updated_at',
                "Failed asserting that 'updated_at' column exists.",
            );
        verify($columns)
            ->arrayHasKey(
                'verification_token',
                "Failed asserting that 'verification_token' column exists.",
            );
    }
}
