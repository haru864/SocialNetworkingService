<?php

namespace Database\Migrations;

use Database;

class FixTweetMessageChars implements Database\SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return [
            "ALTER TABLE tweets MODIFY message VARCHAR(200) NOT NULL",
            "ALTER TABLE tweets CHANGE media_file_path media_file_name varchar(255)",
        ];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [
            "ALTER TABLE tweets MODIFY message VARCHAR(255) NOT NULL",
            "ALTER TABLE tweets CHANGE media_file_name media_file_path varchar(255)",
        ];
    }
}
