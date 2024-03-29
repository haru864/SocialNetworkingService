<?php

namespace Database\Migrations;

use Database;

class ExpandFilenameLengthInUsers implements Database\SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return [
            "ALTER TABLE users MODIFY COLUMN profile_image varchar(80);"
        ];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [
            "ALTER TABLE users MODIFY COLUMN profile_image varchar(64);"
        ];
    }
}
