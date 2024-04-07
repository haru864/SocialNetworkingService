<?php

namespace Database\Migrations;

use Database;

class AddUniqueKeyToRetweets implements Database\SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return [
            "ALTER TABLE retweets ADD CONSTRAINT UK1 UNIQUE(user_id, tweet_id)"
        ];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [
            "ALTER TABLE retweets DROP COLUMN user_id"
        ];
    }
}
