<?php

namespace Database\Migrations;

use Database;

class CreateTables implements Database\SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return [
            "CREATE TABLE users (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                name varchar(15) UNIQUE NOT NULL,
                password_hash varchar(255) NOT NULL,
                email varchar(100) NOT NULL,
                self_introduction varchar(50),
                profile_image varchar(80),
                created_at DATETIME NOT NULL,
                last_login DATETIME NOT NULL
            )",
            "CREATE TABLE careers (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT NOT NULL,
                job VARCHAR(100),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
            )",
            "CREATE TABLE addresses (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT NOT NULL,
                country VARCHAR(100),
                state VARCHAR(100),
                city VARCHAR(100),
                town VARCHAR(100),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
            )",
            "CREATE TABLE hobbies (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT NOT NULL,
                hobby VARCHAR(100),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
            )",
            "CREATE TABLE tweets (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                reply_to_id BIGINT,
                retweet_to_id BIGINT,
                user_id BIGINT NOT NULL,
                message VARCHAR(200) NOT NULL,
                media_file_name VARCHAR(255),
                media_type VARCHAR(255),
                posting_datetime DATETIME NOT NULL,
                FOREIGN KEY (reply_to_id) REFERENCES tweets(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (retweet_to_id) REFERENCES tweets(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
            )",
            "CREATE TABLE follows (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                follower_id BIGINT NOT NULL,
                followee_id BIGINT NOT NULL,
                follow_datetime DATETIME NOT NULL,
                UNIQUE (follower_id, followee_id),
                INDEX (follower_id),
                FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (followee_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
            )",
            "CREATE TABLE likes (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT NOT NULL,
                tweet_id BIGINT NOT NULL,
                like_datetime DATETIME NOT NULL,
                UNIQUE(user_id, tweet_id),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (tweet_id) REFERENCES tweets(id) ON DELETE CASCADE ON UPDATE CASCADE
            )",
            "CREATE TABLE messages (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                sender_id BIGINT NOT NULL,
                recipient_id BIGINT NOT NULL,
                message varchar(200) NOT NULL,
                media_file_name VARCHAR(255),
                media_type VARCHAR(255),
                send_datetime DATETIME NOT NULL,
                FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
            )",
            "CREATE TABLE scheduled_tweets (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                reply_to_id BIGINT,
                user_id BIGINT NOT NULL,
                message VARCHAR(200) NOT NULL,
                media_file_name VARCHAR(255),
                media_type VARCHAR(255),
                scheduled_datetime DATETIME NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
            )",
        ];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [
            "DROP TABLE users",
            "DROP TABLE careers",
            "DROP TABLE addresses",
            "DROP TABLE hobbies",
            "DROP TABLE tweets",
            "DROP TABLE follows",
            "DROP TABLE likes",
            "DROP TABLE messages",
            "DROP TABLE scheduled_tweets",
        ];
    }
}
