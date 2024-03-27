<?php

namespace Database\Migrations;

use Database;

class ModifyColumnNameInUsers implements Database\SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return [
            "ALTER TABLE users CHANGE COLUMN registration created_at DATETIME NOT NULL"
        ];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [
            "ALTER TABLE users CHANGE COLUMN created_at registration DATETIME NOT NULL"
        ];
    }
}
