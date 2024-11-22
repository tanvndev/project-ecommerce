<?php

// Trong Laravel, Service Pattern thường được sử dụng để tạo các lớp service, giúp tách biệt logic của ứng dụng khỏi controller.

namespace App\Services\Chat;

use App\Classes\Upload;
use App\Events\Chat\MessageSentEvent;
use App\Models\Chat;
use App\Models\User;
use App\Repositories\Interfaces\Chat\ChatRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use App\Services\BaseService;
use App\Services\Interfaces\Chat\ChatServiceInterface;
use Exception;

class ChatService extends BaseService implements ChatServiceInterface
{
    public function __construct(
        protected ChatRepositoryInterface $chatRepository,
        protected UserRepositoryInterface $userRepository
    ) {}

    public function getChatList()
    {
        try {
            $request = request();

            $pageSize = $request->input('pageSize', 15);
            $searchTerm = $request->input('search', '');

            $users = $this->userRepository->findChatList(
                ['user_catalogue_id' => ['!=', User::ROLE_ADMIN]],
                ['*'],
                [
                    [
                        'sent_chats' => function ($query) {
                            $query->orderBy('created_at', 'desc')->take(1);
                        },
                    ],
                    [
                        'received_chats' => function ($query) {
                            $query->orderBy('created_at', 'desc')->take(1);
                        },
                    ],
                ],
                $searchTerm,
                $pageSize
            );

            foreach ($users as $user) {
                $lastSentChat = $user->sent_chats->first();
                $lastReceivedChat = $user->received_chats->first();

                $lastMessageData = $this->getLastMessage($lastSentChat, $lastReceivedChat);

                $user->last_message = $lastMessageData['message'];
                $user->last_at = $lastMessageData['last_at'];
            }

            return successResponse('', $users);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function getChatListUser()
    {
        try {
            $users = $this->userRepository->findByWhere(
                ['user_catalogue_id' => ['!=', User::ROLE_CUSTOMER]],
                ['*'],
                [
                    [
                        'sent_chats' => function ($query) {
                            $query->orderBy('created_at', 'desc')->take(1);
                        },
                    ],
                    [
                        'received_chats' => function ($query) {
                            $query->orderBy('created_at', 'desc')->take(1);
                        },
                    ],
                ],
                true
            );

            foreach ($users as $user) {
                $lastSentChat = $user->sent_chats->first();
                $lastReceivedChat = $user->received_chats->first();

                $lastMessageData = $this->getLastMessage($lastSentChat, $lastReceivedChat);

                $user->last_message = $lastMessageData['message'];
                $user->last_at = $lastMessageData['last_at'];
            }

            return $users;
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    private function getLastMessage($lastSentChat, $lastReceivedChat)
    {
        if ($lastSentChat && $lastReceivedChat) {
            if ($lastSentChat->created_at > $lastReceivedChat->created_at) {
                return [
                    'message' => $lastSentChat->message,
                    'last_at' => $lastSentChat->created_at,
                ];
            } else {
                return [
                    'message' => $lastReceivedChat->message,
                    'last_at' => $lastReceivedChat->created_at,
                ];
            }
        }

        if ($lastSentChat) {
            return [
                'message' => $lastSentChat->message,
                'last_at' => $lastSentChat->created_at,
            ];
        }

        if ($lastReceivedChat) {
            return [
                'message' => $lastReceivedChat->message,
                'last_at' => $lastReceivedChat->created_at,
            ];
        }

        return [
            'message' => null,
            'last_at' => null,
        ];
    }

    public function sendMessage(string $receiverId)
    {
        return $this->executeInTransaction(function () use ($receiverId) {
            $payload = $this->prearePayload($receiverId);

            $chat = $this->chatRepository->create($payload);

            event(new MessageSentEvent($chat));

            return successResponse(__('messages.create.success'), $chat);
        }, __('messages.create.error'));
    }

    private function prearePayload(string $receiverId): array
    {

        $payload = request()->except('_token', '_method');
        $payload['sender_id'] = auth()->id();
        $payload['receiver_id'] = $receiverId;

        $uploadedImages = [];
        if (isset($payload['images']) && ! empty($payload['images'])) {
            $uploadResponse = $this->handleImageUploads($payload['images']);

            if ($uploadResponse['status'] === 'error') {
                return errorResponse($uploadResponse['message']);
            }

            $uploadedImages = $uploadResponse['data'];
        }
        $payload['images'] = count($uploadedImages) > 0 ? $uploadedImages : null;

        return $payload;
    }

    private function handleImageUploads(array $images): array
    {
        $uploadedImages = [];

        foreach ($images as $image) {
            $uploadResponse = Upload::uploadImage($image);

            if ( ! $uploadResponse['status'] === 'success') {
                return [
                    'status'  => 'error',
                    'message' => $uploadResponse['message'],
                ];
            }

            $uploadedImages[] = $uploadResponse['data'];
        }

        return [
            'status'  => 'success',
            'data'    => $uploadedImages,
        ];
    }

    public function getMessage(string $senderId)
    {
        try {
            $chats = Chat::where(function ($query) use ($senderId) {
                $query->where('sender_id', $senderId)
                    ->where('receiver_id', auth()->id());
            })
                ->orWhere(function ($query) use ($senderId) {
                    $query->where('sender_id', auth()->id())
                        ->where('receiver_id', $senderId);
                })
                ->with(['sender', 'receiver'])
                ->orderBy('created_at', 'asc')
                ->get();

            Chat::where([
                'sender_id' => $senderId,
                'read_at'   => false,
            ])->update(['read_at' => true]);

            return successResponse(__('messages.retrieve.success'), $chats);
        } catch (Exception $e) {
            return errorResponse('Get message error.');
        }
    }
}
