<?php

namespace Database\DataAccess\Implementations;

use Database\DataAccess\Interfaces\MessagesDAO;
use Database\DatabaseManager;
use Exceptions\QueryFailedException;
use Models\Message;
use Settings\Settings;

class MessagesDAOImpl implements MessagesDAO
{
    public function create(Message $message): Message
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            INSERT INTO messages (
                sender_id,
                recipient_id,
                encrypted_message,
                media_file_name,
                media_type,
                send_datetime
            )
            VALUES (
                ?, ?, ?, ?, ?, ?
            )
        SQL;
        $result = $mysqli->prepareAndExecute(
            $query,
            'iissss',
            [
                $message->getSenderId(),
                $message->getRecipientId(),
                $this->encryptMessage($message->getMessage()),
                $message->getMediaFileName(),
                $message->getMediaType(),
                $message->getSendDatetime()
            ],
        );
        if (!$result) {
            throw new QueryFailedException("INSERT INTO failed.");
        }
        $message->setId($mysqli->insert_id);
        return $message;
    }

    public function getMessagesBySenderId(int $senderId, int $limit, int $offset): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            SELECT * FROM messages
            WHERE sender_id = ?
            LIMIT ?
            OFFSET ?
        SQL;
        $records = $mysqli->prepareAndFetchAll($query, 'iii', [$senderId, $limit, $offset]);
        return $records === null ? [] : $this->convertRecordArrayToMessageArray($records);
    }

    public function getMessageById(int $messageId): ?Message
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            SELECT * FROM messages WHERE id = ?
        SQL;
        $records = $mysqli->prepareAndFetchAll($query, 'i', [$messageId]);
        return $records === null ? null : $this->convertRecordToMessage($records[0]);
    }

    public function getSenders(int $userId): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            SELECT DISTINCT sender_id FROM messages
            WHERE recipient_id = ?
        SQL;
        $records = $mysqli->prepareAndFetchAll($query, 'i', [$userId]);
        return $records;
    }

    public function getRecipients(int $userId): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            SELECT DISTINCT recipient_id FROM messages
            WHERE sender_id = ?
        SQL;
        $records = $mysqli->prepareAndFetchAll($query, 'i', [$userId]);
        return $records;
    }

    public function getMessageExchanges(int $yourUserId, int $otherUserId, int $limit, int $offset): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            SELECT * FROM messages
            WHERE (sender_id = ? AND recipient_id = ?)
                OR (sender_id = ? AND recipient_id = ?)
            ORDER BY id DESC
            LIMIT ?
            OFFSET ?
        SQL;
        $records = $mysqli->prepareAndFetchAll(
            $query,
            'iiiiii',
            [$yourUserId, $otherUserId, $otherUserId, $yourUserId, $limit, $offset]
        );
        return $records === null ? null : $this->convertRecordArrayToMessageArray($records);
    }

    public function deleteMessageExchanges(int $yourUserId, int $otherUserId): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            DELETE FROM messages
            WHERE (sender_id = ? AND recipient_id = ?)
                OR (sender_id = ? AND recipient_id = ?)
        SQL;
        return $mysqli->prepareAndExecute(
            $query,
            'iiii',
            [$yourUserId, $otherUserId, $otherUserId, $yourUserId]
        );
    }

    private function convertRecordArrayToMessageArray(array $records): array
    {
        $messages = [];
        foreach ($records as $record) {
            $message = $this->convertRecordToMessage($record);
            array_push($messages, $message);
        }
        return $messages;
    }

    private function convertRecordToMessage(array $data): Message
    {
        $message = new Message(
            id: $data['id'],
            senderId: $data['sender_id'],
            recipientId: $data['recipient_id'],
            message: $this->decryptMessage($data['encrypted_message']),
            mediaFileName: $data['media_file_name'],
            mediaType: $data['media_type'],
            sendDatetime: $data['send_datetime']
        );
        return $message;
    }

    private function encryptMessage(string $message): string
    {
        $cipher = Settings::env('ENCRYPTION_CIPHER');
        $key = Settings::env('ENCRYPTION_KEY');
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
        $encrypted_message = openssl_encrypt($message, $cipher, $key, 0, $iv);
        $encrypted_message_with_iv = base64_encode($iv . $encrypted_message);
        return $encrypted_message_with_iv;
    }

    private function decryptMessage(string $encrypted_message_with_iv): string
    {
        $cipher = Settings::env('ENCRYPTION_CIPHER');
        $key = Settings::env('ENCRYPTION_KEY');
        $encrypted_message_with_iv = base64_decode($encrypted_message_with_iv);
        $iv_length = openssl_cipher_iv_length($cipher);
        $iv = substr($encrypted_message_with_iv, 0, $iv_length);
        $encrypted_message = substr($encrypted_message_with_iv, $iv_length);
        $decrypted_message = openssl_decrypt($encrypted_message, $cipher, $key, 0, $iv);
        return $decrypted_message;
    }
}
