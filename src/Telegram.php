<?php
namespace Koyabu\TelegramAPI;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Telegram {

    protected $token;
    protected $botname;
    protected $apiUrl;
    protected $client;

    function __construct($option) {

        if (!isset($option['token'])) {
            throw new Exception("Telegram API token required!");
        }

        $this->token = $option['token'];
        $this->botname = $option['botname'] ?? '';
        $this->apiUrl = "https://api.telegram.org/bot{$this->token}/";

        $this->client = new Client([
            'base_uri' => $this->apiUrl,
            'timeout'  => 30,
        ]);
    }

    private function request($method, $params = [], $multipart = false) {
        try {
            $options = [];

            if ($multipart) {
                $form = [];
                foreach ($params as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $v) {
                            $form[] = ['name' => $key.'[]', 'contents' => $v];
                        }
                    } else {
                        $form[] = ['name' => $key, 'contents' => $value];
                    }
                }
                $options['multipart'] = $form;
            } else {
                $options['form_params'] = $params;
            }

            $response = $this->client->post($method, $options);
            return json_decode($response->getBody()->getContents(), true);

        } catch (RequestException $e) {
            throw new Exception($e->getMessage());
        }
    }

    // ============================================================
    // SEND MESSAGE / MEDIA
    // ============================================================

    public function send($chat_id, $text, $file = '', $caption = '', $parse_mode = 'Markdown') {
        if ($file === '') {
            return $this->request('sendMessage', [
                'chat_id' => $chat_id,
                'text' => $text,
                'parse_mode' => $parse_mode
            ]);
        }

        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
            return $this->sendPhoto($chat_id, $file, $caption ?: $text);
        } elseif (in_array($ext, ['mp4','mov'])) {
            return $this->sendVideo($chat_id, $file, $caption ?: $text);
        } elseif (in_array($ext, ['mp3'])) {
            return $this->sendAudio($chat_id, $file, $caption ?: $text);
        } else {
            return $this->sendDocument($chat_id, $file, $caption ?: $text);
        }
    }

    public function sendMessage($chat_id, $text, $parse_mode = 'Markdown') {
        return $this->request('sendMessage', [
            'chat_id' => $chat_id,
            'text' => $text,
            'parse_mode' => $parse_mode
        ]);
    }

    public function sendPhoto($chat_id, $file, $caption = '') {
        return $this->request('sendPhoto', [
            'chat_id' => $chat_id,
            'photo' => fopen($file, 'r'),
            'caption' => $caption
        ], true);
    }

    public function sendDocument($chat_id, $file, $caption = '') {
        return $this->request('sendDocument', [
            'chat_id' => $chat_id,
            'document' => fopen($file, 'r'),
            'caption' => $caption
        ], true);
    }

    public function sendAudio($chat_id, $file, $caption = '') {
        return $this->request('sendAudio', [
            'chat_id' => $chat_id,
            'audio' => fopen($file, 'r'),
            'caption' => $caption
        ], true);
    }

    public function sendVideo($chat_id, $file, $caption = '') {
        return $this->request('sendVideo', [
            'chat_id' => $chat_id,
            'video' => fopen($file, 'r'),
            'caption' => $caption
        ], true);
    }

    public function sendVoice($chat_id, $file, $caption = '') {
        return $this->request('sendVoice', [
            'chat_id' => $chat_id,
            'voice' => fopen($file, 'r'),
            'caption' => $caption
        ], true);
    }

    public function sendChatAction($chat_id, $action = 'typing') {
        return $this->request('sendChatAction', [
            'chat_id' => $chat_id,
            'action' => $action
        ]);
    }

    public function sendMediaGroup($chat_id, array $files) {
        $media = [];
        $multipart = [];

        foreach ($files as $i => $file) {
            $fileId = "file{$i}";

            $media[] = [
                'type' => $file['type'],
                'media' => "attach://{$fileId}",
                'caption' => $file['caption'] ?? ''
            ];

            $multipart[] = [
                'name' => $fileId,
                'contents' => fopen($file['file'], 'r'),
            ];
        }

        $multipart[] = [
            'name' => 'chat_id',
            'contents' => $chat_id
        ];
        $multipart[] = [
            'name' => 'media',
            'contents' => json_encode($media)
        ];

        return $this->request('sendMediaGroup', $multipart, true);
    }

    // ============================================================
    // WEBHOOK CONTROL
    // ============================================================

    public function setWebhook($url) {
        return $this->request('setWebhook', ['url' => $url]);
    }

    public function deleteWebhook() {
        return $this->request('deleteWebhook');
    }

    public function getWebhookInfo() {
        return $this->request('getWebhookInfo');
    }

    // ============================================================
    // UPDATES (LONG POLLING)
    // ============================================================

    public function getUpdates($start = 0, $limit = 100) {
        return $this->request('getUpdates', [
            'offset' => $start,
            'limit'  => $limit,
            'timeout' => 0
        ]);
    }

    // ============================================================
    // EXTRA TOOLS
    // ============================================================

    public function getMe() {
        return $this->request('getMe');
    }

    public function getFile($file_id) {
        return $this->request('getFile', ['file_id' => $file_id]);
    }

    public function buildFileUrl($file_path) {
        return "https://api.telegram.org/file/bot{$this->token}/{$file_path}";
    }

    public function editMessageText($chat_id, $message_id, $text) {
        return $this->request('editMessageText', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => $text
        ]);
    }

    public function deleteMessage($chat_id, $message_id) {
        return $this->request('deleteMessage', [
            'chat_id' => $chat_id,
            'message_id' => $message_id
        ]);
    }
}
?>