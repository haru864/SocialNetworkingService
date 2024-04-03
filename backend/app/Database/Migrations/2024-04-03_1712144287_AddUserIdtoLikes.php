<?php

namespace Database\Migrations;

use Database;

class AddUserIdtoLikes implements Database\SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return [
            "ALTER TABLE likes ADD user_id INT NOT NULL AFTER id",
            "ALTER TABLE likes ADD FOREIGN KEY (user_id) REFERENCES users(id)",
            "ALTER TABLE likes ADD CONSTRAINT UK1 UNIQUE(user_id, tweet_id)",
        ];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [
            "ALTER TABLE likes DROP CONSTRAINT likes_ibfk_2",
            "ALTER TABLE likes DROP CONSTRAINT UK1",
            "ALTER TABLE likes DROP COLUMN user_id",
        ];
    }
}