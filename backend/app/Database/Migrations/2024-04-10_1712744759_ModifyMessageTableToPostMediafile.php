<?php

namespace Database\Migrations;

use Database;

class ModifyMessageTableToPostMediafile implements Database\SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return [
            "ALTER TABLE messages MODIFY message VARCHAR(200) NOT NULL",
            "ALTER TABLE messages ADD COLUMN media_file_name VARCHAR(255) AFTER message",
            "ALTER TABLE messages ADD COLUMN media_type VARCHAR(255) AFTER media_file_name",
        ];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [
            "ALTER TABLE messages MODIFY message VARCHAR(255) NOT NULL",
            "ALTER TABLE messages DROP COLUMN media_file_name",
            "ALTER TABLE messages DROP COLUMN media_type",
        ];
    }
}