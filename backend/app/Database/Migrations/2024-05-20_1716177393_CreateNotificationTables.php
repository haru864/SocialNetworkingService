<?php

namespace Database\Migrations;

use Database;

class CreateNotificationTables implements Database\SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return [
            "CREATE TABLE reply_notifications (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                notified_user_id BIGINT NOT NULL,
                reply_id BIGINT NOT NULL,
                is_confirmed BOOLEAN NOT NULL,
                created_at DATETIME NOT NULL,
                FOREIGN KEY (notified_user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (reply_id) REFERENCES tweets(id) ON DELETE CASCADE ON UPDATE CASCADE
            )",
            "CREATE TABLE retweet_notifications (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                notified_user_id BIGINT NOT NULL,
                retweet_id BIGINT NOT NULL,
                is_confirmed BOOLEAN NOT NULL,
                created_at DATETIME NOT NULL,
                FOREIGN KEY (notified_user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (retweet_id) REFERENCES tweets(id) ON DELETE CASCADE ON UPDATE CASCADE
            )",
            "CREATE TABLE follow_notifications (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                notified_user_id BIGINT NOT NULL,
                follow_id BIGINT NOT NULL,
                is_confirmed BOOLEAN NOT NULL,
                created_at DATETIME NOT NULL,
                FOREIGN KEY (notified_user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (follow_id) REFERENCES follows(id) ON DELETE CASCADE ON UPDATE CASCADE
            )",
            "CREATE TABLE like_notifications (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                notified_user_id BIGINT NOT NULL,
                like_id BIGINT NOT NULL,
                is_confirmed BOOLEAN NOT NULL,
                created_at DATETIME NOT NULL,
                FOREIGN KEY (notified_user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (like_id) REFERENCES likes(id) ON DELETE CASCADE ON UPDATE CASCADE
            )",
            "CREATE TABLE message_notifications (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                notified_user_id BIGINT NOT NULL,
                message_id BIGINT NOT NULL,
                is_confirmed BOOLEAN NOT NULL,
                created_at DATETIME NOT NULL,
                FOREIGN KEY (notified_user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (message_id) REFERENCES messages(id) ON DELETE CASCADE ON UPDATE CASCADE
            )",
        ];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [
            "DROP TABLE reply_notifications",
            "DROP TABLE retweet_notifications",
            "DROP TABLE follow_notifications",
            "DROP TABLE like_notifications",
            "DROP TABLE message_notifications",
        ];
    }
}
