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
                id INT AUTO_INCREMENT PRIMARY KEY,
                name varchar(15) UNIQUE NOT NULL,
                password_hash varchar(255) NOT NULL,
                email varchar(100) NOT NULL,
                self_introduction varchar(50),
                profile_image varchar(80),
                created_at DATETIME NOT NULL,
                last_login DATETIME NOT NULL
            )",
            "CREATE TABLE careers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                job VARCHAR(100),
                FOREIGN KEY (user_id) REFERENCES users(id)
                ON DELETE CASCADE
                ON UPDATE CASCADE
            )",
            "CREATE TABLE addresses (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                country VARCHAR(100),
                state VARCHAR(100),
                city VARCHAR(100),
                town VARCHAR(100),
                FOREIGN KEY (user_id) REFERENCES users(id)
                ON DELETE CASCADE
                ON UPDATE CASCADE
            )",
            "CREATE TABLE hobbies (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                hobby VARCHAR(100),
                FOREIGN KEY (user_id) REFERENCES users(id)
                ON DELETE CASCADE
                ON UPDATE CASCADE
            )",
            "CREATE TABLE tweets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                reply_to_id INT,
                user_id INT NOT NULL,
                message VARCHAR(200) NOT NULL,
                media_file_name VARCHAR(255),
                media_type VARCHAR(255),
                posting_datetime DATETIME NOT NULL,
                FOREIGN KEY (reply_to_id) REFERENCES tweets(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
            )",
            "CREATE TABLE retweets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                tweet_id INT NOT NULL,
                retweet_datetime DATETIME NOT NULL,
                UNIQUE(user_id, tweet_id),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (tweet_id) REFERENCES tweets(id) ON DELETE CASCADE ON UPDATE CASCADE
            )",
            "CREATE TABLE follows (
                id INT AUTO_INCREMENT PRIMARY KEY,
                follower_id INT NOT NULL,
                followee_id INT NOT NULL,
                follow_datetime DATETIME NOT NULL,
                UNIQUE (follower_id, followee_id),
                INDEX (follower_id),
                FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (followee_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
            )",
            "CREATE TABLE likes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                tweet_id INT NOT NULL,
                like_datetime DATETIME NOT NULL,
                UNIQUE(user_id, tweet_id),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (tweet_id) REFERENCES tweets(id) ON DELETE CASCADE ON UPDATE CASCADE
            )",
            "CREATE TABLE messages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                sender_id INT NOT NULL,
                recipient_id INT NOT NULL,
                message varchar(200) NOT NULL,
                media_file_name VARCHAR(255),
                media_type VARCHAR(255),
                send_datetime DATETIME NOT NULL,
                FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
            )",
            "CREATE TABLE retweet_notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                retweet_id INT NOT NULL,
                is_confirmed BOOLEAN NOT NULL,
                created_at DATETIME NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (retweet_id) REFERENCES retweets(id) ON DELETE CASCADE ON UPDATE CASCADE
            )",
            "CREATE TABLE follow_notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                follow_id INT NOT NULL,
                is_confirmed BOOLEAN NOT NULL,
                created_at DATETIME NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (follow_id) REFERENCES follows(id) ON DELETE CASCADE ON UPDATE CASCADE
            )",
            "CREATE TABLE like_notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                like_id INT NOT NULL,
                is_confirmed BOOLEAN NOT NULL,
                created_at DATETIME NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (like_id) REFERENCES likes(id) ON DELETE CASCADE ON UPDATE CASCADE
            )",
            "CREATE TABLE message_notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                message_id INT NOT NULL,
                is_confirmed BOOLEAN NOT NULL,
                created_at DATETIME NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (message_id) REFERENCES messages(id) ON DELETE CASCADE ON UPDATE CASCADE
            )",
            "CREATE TABLE scheduled_tweets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                message VARCHAR(255) NOT NULL,
                media_file_path VARCHAR(255),
                media_type VARCHAR(255),
                scheduled_datetime DATETIME NOT NULL
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
            "DROP TABLE retweets",
            "DROP TABLE follows",
            "DROP TABLE likes",
            "DROP TABLE messages",
            "DROP TABLE retweet_notifications",
            "DROP TABLE follow_notifications",
            "DROP TABLE like_notifications",
            "DROP TABLE message_notifications",
            "DROP TABLE scheduled_tweets",
        ];
    }
}
