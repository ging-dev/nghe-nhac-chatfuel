<?php

declare(strict_types=1);

namespace Chatfuel;

class Chatfuel
{
    public const VERSION = '1.0.0';

    protected $response = [];

    public function __construct($debug = false)
    {
        if ((! $debug) && (! isset($_SERVER['HTTP_USER_AGENT']) || strpos($_SERVER['HTTP_USER_AGENT'], 'Apache-HttpAsyncClient') === false)) {
            exit;
        }
    }

    public function __destruct()
    {
        if (count($this->response) > 0) {
            try {
                header('Content-Type: application/json');
                echo json_encode(['messages' => $this->response]);
                exit;
            } catch (Exception $e) {
                // noop
            }
        }
    }

    public function sendText($messages = null)
    {
        if (null === $messages) {
            throw new Exception('Invalid input', 1);
        }

        $type = gettype($messages);
        if ($type === 'string') {
            $this->response[] = ['text' => $messages];
        } elseif ($type === 'array' || is_array($messages)) {
            foreach ($messages as $message) {
                $this->response[] = ['text' => $message];
            }
        } else {
            $this->response[] = ['text' => 'Error!'];
        }
    }

    public function sendImage($url)
    {
        if ($this->isURL($url)) {
            $this->sendAttachment('image', ['url' => $url]);
        } else {
            $this->sendText('Error: Invalid URL!');
        }
    }

    public function sendVideo($url)
    {
        if ($this->isURL($url)) {
            $this->sendAttachment('video', ['url' => $url]);
        } else {
            $this->sendText('Error: Invalid URL!');
        }
    }

    public function sendAudio($url)
    {
        if ($this->isURL($url)) {
            $this->sendAttachment('audio', ['url' => $url]);
        } else {
            $this->sendText('Error: Invalid URL!');
        }
    }

    public function sendTextCard($text, $buttons)
    {
        if (is_array($buttons)) {
            $this->sendAttachment('template', [
        'template_type' => 'button',
        'text'          => $text,
        'buttons'       => $buttons,
      ]);

            return true;
        }

        return false;
    }

    public function sendGallery($elements)
    {
        if (is_array($elements)) {
            $this->sendAttachment('template', [
        'template_type' => 'generic',
        'elements'      => $elements,
      ]);

            return true;
        }

        return false;
    }

    public function createElement($title, $image, $subTitle, $buttons)
    {
        if ($this->isURL($image) && is_array($buttons)) {
            return [
        'title'     => $title,
        'image_url' => $image,
        'subtitle'  => $subTitle,
        'buttons'   => $buttons,
      ];
        }

        return false;
    }

    public function createButtonToBlock($title, $block, $setAttributes = null)
    {
        $button = [];
        $button['type'] = 'show_block';
        $button['title'] = $title;

        if (is_array($block)) {
            $button['block_names'] = $block;
        } else {
            $button['block_name'] = $block;
        }

        if (null !== $setAttributes && is_array($setAttributes)) {
            $button['set_attributes'] = $setAttributes;
        }

        return $button;
    }

    public function createButtonToURL($title, $url, $setAttributes = null)
    {
        if ($this->isURL($url)) {
            $button = [];
            $button['type'] = 'web_url';
            $button['url'] = $url;
            $button['title'] = $title;

            if (null !== $setAttributes && is_array($setAttributes)) {
                $button['set_attributes'] = $setAttributes;
            }

            return $button;
        }

        return false;
    }

    public function createPostBackButton($title, $url)
    {
        if ($this->isURL($url)) {
            return [
        'url'   => $url,
        'type'  => 'json_plugin_url',
        'title' => $title,
      ];
        }

        return false;
    }

    public function createCallButton($phoneNumber, $title = 'Call')
    {
        return [
      'type'         => 'phone_number',
      'phone_number' => $phoneNumber,
      'title'        => $title,
    ];
    }

    public function createShareButton()
    {
        return ['type' => 'element_share'];
    }

    public function createQuickReply($text, $quickReplies)
    {
        if (is_array($quickReplies)) {
            $this->response['text'] = $text;
            $this->response['quick_replies'] = $quickReplies;

            return true;
        }

        return false;
    }

    public function createQuickReplyButton($title, $block)
    {
        $button = [];
        $button['title'] = $title;

        if (is_array($block)) {
            $button['block_names'] = $block;
        } else {
            $button['block_name'] = $block;
        }

        return $button;
    }

    private function sendAttachment($type, $payload)
    {
        $type = strtolower($type);
        $validTypes = ['image', 'video', 'audio', 'template'];

        if (in_array($type, $validTypes)) {
            $this->response[] = [
        'attachment' => [
          'type'    => $type,
          'payload' => $payload,
        ],
      ];
        } else {
            $this->response[] = ['text' => 'Error: Invalid type!'];
        }
    }

    private function isURL($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }
}
