<?php

namespace Services;

use Database\DataAccess\Implementations\MessagesDAOImpl;
use Database\DataAccess\Implementations\UsersDAOImpl;
use Exceptions\InvalidRequestParameterException;
use Helpers\FileUtility;
use Helpers\SessionManager;
use Helpers\ValidationHelper;
use Http\Request\PostMessageRequest;
use Models\Message;
use Settings\Settings;

class MessageService
{
    private MessagesDAOImpl $messagesDAOImpl;
    private UsersDAOImpl $usersDAOImpl;

    public function __construct(MessagesDAOImpl $messagesDAOImpl, UsersDAOImpl $usersDAOImpl)
    {
        $this->messagesDAOImpl = $messagesDAOImpl;
        $this->usersDAOImpl = $usersDAOImpl;
    }

    public function createMessage(PostMessageRequest $request): void
    {
        $currentDatetime = date('Y-m-d H:i:s');
        $maxMsgChars = 200;
        if (mb_strlen($request->getMessage()) < 1 || mb_strlen($request->getMessage()) > $maxMsgChars) {
            throw new InvalidRequestParameterException("Message must be at least 1 character and no more than {$maxMsgChars} characters");
        }
        if (is_null($request->getMedia())) {
            $mediaFileName = null;
            $mimeType = null;
        } else {
            $fileInfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $fileInfo->file($request->getMedia()['tmp_name']);
            if (strpos($mimeType, 'image/') === 0) {
                ValidationHelper::validateUploadedImage('media');
                $mediaFileName = FileUtility::storeImageWithThumbnail(
                    storeDirPath: Settings::env('IMAGE_FILE_LOCATION_DM_UPLOAD'),
                    thumbDirPath: Settings::env('IMAGE_FILE_LOCATION_DM_THUMBNAIL'),
                    uploadedTmpFilePath: $request->getMedia()['tmp_name'],
                    uploadedFileName: $request->getMedia()['name'],
                    thumbWidth: 200
                );
            } else if (strpos($mimeType, 'video/') === 0) {
                ValidationHelper::validateUploadedVideo('media');
                $mediaFileName = FileUtility::storeVideo(
                    storeDirPath: Settings::env('VIDEO_FILE_LOCATION_DM'),
                    uploadedTmpFilePath: $request->getMedia()['tmp_name'],
                    uploadedFileName: $request->getMedia()['name']
                );
            } else {
                throw new InvalidRequestParameterException("Attachments to message must be images or videos.");
            }
        }
        $message = new Message(
            id: null,
            senderId: SessionManager::get('user_id'),
            recipientId: $request->getRecipientUserId(),
            message: $request->getMessage(),
            mediaFileName: $mediaFileName,
            mediaType: $mimeType,
            sendDatetime: $currentDatetime
        );
        $this->messagesDAOImpl->create($message);
        return;
    }

    public function getChats(int $userId): ?array
    {
        $senderIds = $this->messagesDAOImpl->getSenders($userId);
        $recipientIds = $this->messagesDAOImpl->getRecipients($userId);
        $dmPartnerIds = [];
        foreach ($senderIds as $key => $value) {
            array_push($dmPartnerIds, $value['sender_id']);
        }
        foreach ($recipientIds as $key => $value) {
            array_push($dmPartnerIds, $value['recipient_id']);
        }
        $dmPartnerIdsUnique =  array_unique($dmPartnerIds);
        $chats = ["chats" => []];
        foreach ($dmPartnerIdsUnique as $dmPartnerId) {
            $dmPartner = $this->usersDAOImpl->getById($dmPartnerId);
            $latestMsg = $this->messagesDAOImpl->getMessageExchanges($userId, $dmPartnerId, 1, 0)[0];
            array_push(
                $chats["chats"],
                [
                    "chat_partner" => [
                        "id" => $dmPartner->getId(),
                        "name" => $dmPartner->getName()
                    ],
                    "latest_message" => $latestMsg->toArray()
                ]
            );
        }
        return $chats;
    }

    public function getIndividualChat(int $userId, int $recipientUserId, int $limit, int $offset): ?array
    {
        $messages = $this->messagesDAOImpl->getMessageExchanges($userId, $recipientUserId, $limit, $offset);
        $messageJsonList = [];
        foreach ($messages as $message) {
            array_push($messageJsonList, $message->toArray());
        }
        return ["messages" => $messageJsonList];
    }

    public function deleteChat(int $userId, int $recipientUserId): void
    {
        $this->messagesDAOImpl->deleteMessageExchanges($userId, $recipientUserId);
        return;
    }
}
