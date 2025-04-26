<?php

namespace Thadico\FcmNotification\Helpers;

use App\Traits\RabbitMQProducer;
use Exception;
use PhpAmqpLib\Exchange\AMQPExchangeType;

class FcmHelper
{
    use RabbitMQProducer;

    /**
     * Push FCM notification
     *
     * @param array $data
     *
     * @return void
     * @throws Exception
     */
    public function pushFcm(array $data): void
    {
        $deviceTokens = $data['device_tokens'] ?? [];
        $dataPush = $data['data_push'] ?? [];
        $body = [
            'auth' => [
                'type' => 'auto',
                'code' => env('FCM_SERVER_KEY', 'test-fcm-8489f')
            ],
            'title'=> 'Image create successfully',
            'body' => 'Image create successfully',
            'tokens' => $deviceTokens,
            'icon' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTbD_WEwx2zXojnFes2ahtjPNzckUjIP9JCnQ&usqp=CAU',
            'config?' => [
                'options'  => [
                    'analytics_label' => 'fcm-project-id-label'
                ],
                '__android__' => 'array: coming soon',
                '__apns__' => 'array: coming soon',
                'web' => [
                    'headers'  => [
                        'header1' => 'value1'
                    ],
                    'fcm_options' => [
                        'link' => 'https://ai-creator.toprate.io',
                        'analytics_label' => 'fcm-web-id-label'
                    ]
                ]
            ],
            'data' => $dataPush
        ];

        $this->setVhost(config('rabbitmq.vhost-notification'))
             ->pushToExchange($body, config('fcm.exchange'), AMQPExchangeType::DIRECT, config('fcm.routing_key'));
    }
}
