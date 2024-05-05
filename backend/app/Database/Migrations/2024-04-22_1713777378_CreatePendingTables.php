<?php

namespace Database\Migrations;

use Database;

class CreatePendingTables implements Database\SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return [
            "CREATE TABLE pending_users (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT,
                name varchar(15) UNIQUE NOT NULL,
                password_hash varchar(255) NOT NULL,
                email varchar(100) NOT NULL,
                self_introduction varchar(50),
                profile_image varchar(80),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
            )",
            "CREATE TABLE pending_careers (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                pending_user_id BIGINT NOT NULL,
                job VARCHAR(100),
                FOREIGN KEY (pending_user_id) REFERENCES pending_users(id) ON DELETE CASCADE ON UPDATE CASCADE
            )",
            "CREATE TABLE pending_addresses (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                pending_user_id BIGINT NOT NULL,
                country VARCHAR(100),
                state VARCHAR(100),
                city VARCHAR(100),
                town VARCHAR(100),
                FOREIGN KEY (pending_user_id) REFERENCES pending_users(id) ON DELETE CASCADE ON UPDATE CASCADE
            )",
            "CREATE TABLE pending_hobbies (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                pending_user_id BIGINT NOT NULL,
                hobby VARCHAR(100),
                FOREIGN KEY (pending_user_id) REFERENCES pending_users(id) ON DELETE CASCADE ON UPDATE CASCADE
            )",
            "CREATE TABLE email_verification (
                hash varchar(64) PRIMARY KEY,
                pending_user_id BIGINT NOT NULL,
                created_at DATETIME NOT NULL,
                expired_at DATETIME NOT NULL,
                FOREIGN KEY (pending_user_id) REFERENCES pending_users(id) ON DELETE CASCADE ON UPDATE CASCADE
            )"
        ];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [
            "DROP TABLE pending_users",
            "DROP TABLE pending_careers",
            "DROP TABLE pending_addresses",
            "DROP TABLE pending_hobbies",
        ];
    }
}
