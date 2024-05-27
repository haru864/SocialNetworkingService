<?php

namespace Database\Migrations;

use Database;

class CreateTrendTweetsView implements Database\SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        $selectTrendTweets = <<<SQL
            SELECT 
                t.id,
                t.reply_to_id,
                t.retweet_to_id,
                t.user_id,
                t.message,
                t.media_file_name,
                t.media_type,
                t.posting_datetime,
                COALESCE(l.like_count, 0) AS like_count,
                COALESCE(rt.retweet_count, 0) AS retweet_count,
                COALESCE(rp.reply_count, 0) AS reply_count,
                (COALESCE(l.like_count, 0) + COALESCE(rt.retweet_count, 0) + COALESCE(rp.reply_count, 0)) AS total_count
            FROM
                tweets t
            LEFT JOIN (
                SELECT tweet_id, COUNT(*) AS like_count 
                FROM likes 
                WHERE like_datetime > NOW() - INTERVAL 24 HOUR
                GROUP BY tweet_id
            ) l ON t.id = l.tweet_id
            LEFT JOIN (
                SELECT retweet_to_id, COUNT(*) AS retweet_count 
                FROM tweets 
                WHERE posting_datetime > NOW() - INTERVAL 24 HOUR AND reply_to_id IS NOT NULL
                GROUP BY retweet_to_id
            ) rt ON t.id = rt.retweet_to_id
            LEFT JOIN (
                SELECT reply_to_id, COUNT(*) AS reply_count 
                FROM tweets 
                WHERE posting_datetime > NOW() - INTERVAL 24 HOUR AND reply_to_id IS NOT NULL
                GROUP BY reply_to_id
            ) rp ON t.id = rp.reply_to_id
            ORDER BY
                total_count DESC, t.id DESC
        SQL;

        $createViewSql = <<<SQL
            CREATE TABLE trend_tweets_materialized_view AS {$selectTrendTweets}
        SQL;

        $createEventSql = <<<SQL
            CREATE EVENT update_trend_tweets_materialized_view
            ON SCHEDULE EVERY 1 HOUR
            DO
            BEGIN
              TRUNCATE TABLE trend_tweets_materialized_view;
              INSERT INTO trend_tweets_materialized_view
              {$selectTrendTweets}
              ;
            END
        SQL;

        return [
            $createViewSql,
            $createEventSql,
        ];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [
            "DROP VIEW trend_tweets_materialized_view",
            "DROP EVENT update_trend_tweets_materialized_view",
        ];
    }
}
