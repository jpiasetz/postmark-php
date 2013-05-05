<?php

/*
 * This file is part of the Postmark package.
 *
 * (c) John Piasetzki <john@piasetzki.name> *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Postmark;

use JsonSerializable;

class Message implements JsonSerializable {
    private $from;
    private $to;
    private $cc;
    private $bcc;
    private $subject;
    private $tag;
    private $htmlBody;
    private $textBody;
    private $replyTo;
    private $headers = array();
    private $attachments = array();

    public function addTo($address, $name = null) {
        $this->addAddress('to', $address, $name);
    }

    public function addCc($address, $name = null) {
        $this->addAddress('cc', $address, $name);
    }

    public function addBcc($address, $name = null) {
        $this->addAddress('bcc', $address, $name);
    }

    private function addAddress($type, $address, $name = null) {
        if ( !empty($this->$type) ) {
            $this->$type .= ', ';
        }
        $this->$type .= $this->createAddress($address, $name);
    }

    public function setSubject($subject) {
        $this->subject = $subject;
    }

    public function setTag($tag) {
        $this->tag = $tag;
    }

    public function setHtmlBody($message) {
        $this->htmlBody = $message;
    }

    public function setTextBody($message) {
        $this->textBody = $message;
    }

    public function setReplyTo($address, $name = null) {
        $this->replyTo = $this->createAddress($address, $name);
    }

    public function addAttachment($name, $content, $contentType) {
        $this->attachments[] = array(
            'Name' => $name,
            'Content' => base64_encode($content),
            'ContentType' => $contentType
        );
    }

    public function addHeader($name, $value) {
        $this->headers[] = array( 'Name' => $name, 'Value' => $value );
    }

    public function setFrom($address, $name = null) {
        $this->from = $this->createAddress($address, $name);
    }

    private function createAddress($address, $name = null) {
        if (!is_null($name)) {
            if ( 1 === preg_match('/^[A-z0-9 ]*$/', $name)) {
                return str_replace('"', '', $name) . ' <' . $address . '>';
            }
            return '"' . str_replace('"', '', $name) . '" <' . $address . '>';
        }
        return $address;
    }

    public function jsonSerialize() {
        $json = [
            'From'     => $this->from,
            'To'       => $this->to,
            'Cc'       => $this->cc,
            'Bcc'      => $this->bcc,
            'Subject'  => $this->subject,
            'Tag'      => $this->tag,
            'HtmlBody' => $this->htmlBody,
            'TextBody' => $this->textBody,
            'ReplyTo'  => $this->replyTo,
            'Headers'  => $this->headers,
            'Attachments' => $this->attachments,
        ];
        return array_filter($json);
    }
}
