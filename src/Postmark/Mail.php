<?php

/*
 * This file is part of the Postmark package.
 *
 * (c) John Piasetzki <john@piasetzki.name>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Postmark;

use Exception, BadMethodCallException;

class Mail {
    private $apiKey = 'POSTMARK_API_TEST';

    public function __construct($apiKey = null) {
        if (!is_null($apiKey)) {
            $this->apiKey = $apiKey;
        }
    }

    private function curl($endPoint, $messages) {
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'X-Postmark-Server-Token: '.$this->apiKey,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endPoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($messages));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        $return = curl_exec($ch);

        if (false === $return) {
            throw new Exception(curl_error($ch));
        }

        $return   = json_decode($return);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        switch ($httpCode) {
            case 200: return $return;
            case 401: throw new BadMethodCallException('Bad or missing server or user API token.');
            case 422: throw new Exception($return->Message, $return->ErrorCode);
            case 500: throw new Exception('Internal Server Error');
            default:  throw new Exception('Unknown error');
        }
    }

    public function batchSend(array $messages) {
        $endPoint = 'https://api.postmarkapp.com/email/batch';
        return $this->curl($endPoint, $messages);
    }

    public function send(Message $message) {
        $endPoint = 'https://api.postmarkapp.com/email';
        return $this->curl($endPoint, $message);
    }
}
