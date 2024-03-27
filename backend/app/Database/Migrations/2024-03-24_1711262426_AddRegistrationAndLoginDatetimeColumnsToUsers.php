<?php

namespace Database\Migrations;

use Database;

class AddRegistrationAndLoginDatetimeColumnsToUsers implements Database\SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return [
            "ALTER TABLE users ADD registration DATETIME NOT NULL",
            "ALTER TABLE users ADD last_login DATETIME NOT NULL",
        ];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [
            "ALTER TABLE users DROP COLUMN registration",
            "ALTER TABLE users DROP COLUMN last_login",
        ];
    }
}
