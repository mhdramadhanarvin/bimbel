<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WhatsappAPI
{
    protected $version = 'v19.0';
    protected $phone_number_id = 271955262661911;
    protected $template_name;
    protected $token;
    protected $baseUrl;
    protected $to;
    protected $templateComponent;
    protected $appID = 385516887524806;
    protected $sessionID;

    public function __construct()
    {
        $this->baseUrl = 'https://graph.facebook.com/' . $this->version . '/';
        $this->template_name = 'hello-world';
        $this->token = env('META_GRAPH_API_ACCESS_TOKEN');
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function setTo($to)
    {
        $this->to = $to;
    }

    public function setTemplateName($templateName)
    {
        $this->template_name = $templateName;
    }

    public function setTemplateComponent($headerImage, $name, $phoneNumber, $address, $totalItem, $totalPrice)
    {
        $this->templateComponent = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $this->to,
            'type' => 'template',
            'template' => [
                'name' => $this->template_name,
                'language' => [
                    'code' => 'id'
                ],
                'components' => [
                    [
                        'type' => 'header',
                        'parameters' => [
                            [
                                'type' => 'image',
                                'image' => [
                                    'link' => $headerImage
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'body',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $name
                            ],
                            [
                                'type' => 'text',
                                'text' => $phoneNumber
                            ],
                            [
                                'type' => 'text',
                                'text' => $address
                            ],
                            [
                                'type' => 'text',
                                'text' => $totalItem
                            ],
                            [
                                'type' => 'text',
                                'text' => $totalPrice
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function sendMessage()
    {
        try {
            $this->baseUrl .= $this->phone_number_id . '/messages';
            $request = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token
            ])->post($this->baseUrl, $this->templateComponent);

            $response = $request->json();

            return $response;
        } catch (\Throwable $e) {
            Log::error('WhatsappAPI: ' . $e->getMessage());
        }
    }

    public function createSessionUpload($fileSize, $fileType)
    {
        $baseUrl = $this->baseUrl . $this->appID . '/uploads';
        $request = Http::withQueryParameters([
            'file_length' => $fileSize,
            'file_type' => $fileType,
            'access_token' => $this->token
        ])->post($baseUrl);

        $this->sessionID = $request->json()['id'];
    }

    public function uploadImage($pathFile)
    {
        $this->baseUrl .= $this->sessionID;
        // $clearPath = Str::remove('public/', $pathFile);
        $clearPath = Str::remove('public/', asset('storage/' . $pathFile));
        // $photo = fopen($clearPath, 'r');
        $request = Http::withHeaders([
            'Authorization' => 'OAuth ' . $this->token,
            'file_offset' => 0
        ])->attach(
            'data-binary',
            file_get_contents($clearPath),
            basename($clearPath)
        )->post($this->baseUrl);

        return [$request->status(), $request->json()];
    }
}
