<?php

namespace Postmark;

use Postmark\Message;

class MailTest extends \PHPUnit_Framework_TestCase
{
    public function testSend()
    {
        $message = new Message();
        $message->setFrom('sender@example.com');
        $message->addTo('receiver@example.com');
        $message->addCc('copied@example.com');
        $message->addBcc('blank-copied@example.com');
        $message->setSubject('Test');
        $message->setTag('Invitation');
        $message->setHtmlBody('<b>Hello</b>');
        $message->setTextBody('Hello');
        $message->setReplyTo('reply@example.com');
        $message->addHeader('CUSTOM-HEADER', 'value');

        $mail = new Mail();
        $result = $mail->send($message);
        $this->assertObjectHasAttribute('To', $result);
        $this->assertEquals('receiver@example.com', $result->To);
        $this->assertObjectHasAttribute('SubmittedAt', $result);
        $this->assertObjectHasAttribute('MessageID', $result);
        $this->assertObjectHasAttribute('ErrorCode', $result);
        $this->assertEquals(0, (int) $result->ErrorCode);
        $this->assertObjectHasAttribute('Message', $result);
        $this->assertEquals('Test job accepted', $result->Message);
    }

    public function testBatchSend()
    {
        $message = new Message();
        $message->setFrom('sender@example.com');
        $message->addTo('receiver@example.com');
        $message->addCc('copied@example.com');
        $message->addBcc('blank-copied@example.com');
        $message->setSubject('Test');
        $message->setTag('Invitation');
        $message->setHtmlBody('<b>Hello</b>');
        $message->setTextBody('Hello');
        $message->setReplyTo('reply@example.com');
        $message->addHeader('CUSTOM-HEADER', 'value');

        $messages = [$message, $message];

        $mail = new Mail();
        $results = $mail->batchSend($messages);
        $this->assertCount(2, $results);
        foreach ($results as $result) {
            $this->assertObjectHasAttribute('To', $result);
            $this->assertEquals('receiver@example.com', $result->To);
            $this->assertObjectHasAttribute('SubmittedAt', $result);
            $this->assertObjectHasAttribute('MessageID', $result);
            $this->assertObjectHasAttribute('ErrorCode', $result);
            $this->assertEquals(0, (int) $result->ErrorCode);
            $this->assertObjectHasAttribute('Message', $result);
            $this->assertEquals('Test job accepted', $result->Message);
        }
    }
}
