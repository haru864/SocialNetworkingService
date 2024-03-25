<?php

namespace Database\Migrations;

use Database;

class SupportsPasswordHashingAndEmailAuthentication implements Database\SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return [
            "ALTER TABLE users MODIFY COLUMN name varchar(15) NOT NULL",
            "ALTER TABLE users MODIFY COLUMN self_introduction varchar(50)",
            "ALTER TABLE users ADD COLUMN password_hash varchar(255) NOT NULL AFTER name",
            "ALTER TABLE users ADD COLUMN email varchar(100) NOT NULL AFTER password_hash",
            "ALTER TABLE users ADD COLUMN email_verified_at datetime AFTER last_login",
        ];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [
            "ALTER TABLE users DROP COLUMN email_verified_at",
            "ALTER TABLE users DROP COLUMN email",
            "ALTER TABLE users DROP COLUMN password_hash",
            "ALTER TABLE users MODIFY COLUMN name varchar(30) NOT NULL UNIQUE",
            "ALTER TABLE users MODIFY COLUMN self_introduction varchar(50) NOT NULL",
        ];
    }
}
