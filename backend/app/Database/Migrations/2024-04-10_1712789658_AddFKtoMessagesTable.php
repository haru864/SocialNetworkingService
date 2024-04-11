<?php

namespace Database\Migrations;

use Database;

class AddFKtoMessagesTable implements Database\SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return [
            "ALTER TABLE messages ADD CONSTRAINT fk_sender_id FOREIGN KEY (sender_id) REFERENCES users(id)",
            "ALTER TABLE messages ADD CONSTRAINT fk_recipient_id FOREIGN KEY (recipient_id) REFERENCES users(id)",
        ];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [
            "ALTER TABLE messages DROP FOREIGN KEY fk_sender_id",
            "ALTER TABLE messages DROP FOREIGN KEY fk_recipient_id",
        ];
    }
}
