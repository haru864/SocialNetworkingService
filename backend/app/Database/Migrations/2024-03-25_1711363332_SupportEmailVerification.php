<?php

namespace Database\Migrations;

use Database;

class SupportEmailVerification implements Database\SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return [
            "CREATE TABLE email_verification (
                hash varchar(64) PRIMARY KEY,
                user_id INT NOT NULL,
                created_at DATETIME NOT NULL,
                expired_at DATETIME NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
            )",
            "ALTER TABLE users ADD COLUMN profile_image varchar(64) AFTER self_introduction"
        ];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [
            "DROP TABLE email_verification",
            "ALTER TABLE users DROP COLUMN profile_image"
        ];
    }
}
