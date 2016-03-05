<?php

namespace Postmark;

use Postmark\Message;

class MessageTest extends \PHPUnit_Framework_TestCase
{
    public function testAddTo()
    {
        $types = array('To', 'Cc', 'Bcc');
        foreach ($types as $type) {
            $func = 'add'.$type;
            $message = new Message();
            $message->$func('foo@example.org');
            $this->assertEquals('{"'.$type.'":"foo@example.org"}', json_encode($message));

            $message->$func('foo@example.org', 'Foo Last');
            $this->assertEquals('{"'.$type.'":"foo@example.org, Foo Last <foo@example.org>"}', json_encode($message));

            $message->$func('foo@example.org', 'Last, Foo');
            $expected = '{"'.$type.'":"foo@example.org, Foo Last <foo@example.org>, \"Last, Foo\" <foo@example.org>"}';
            $this->assertEquals($expected, json_encode($message));
        }
    }

    /**
     * @covers Postmark\Message::setSubject
     */
    public function testSetSubject()
    {
        $message = new Message();
        $message->setSubject('Foo');
        $this->assertEquals('{"Subject":"Foo"}', json_encode($message));
    }

    /**
     * @covers Postmark\Message::setTag
     */
    public function testSetTag()
    {
        $message = new Message();
        $message->setTag('Admin');
        $this->assertEquals('{"Tag":"Admin"}', json_encode($message));
    }

    /**
     * @covers Postmark\Message::setHtmlBody
     */
    public function testHtmlBody()
    {
        $message = new Message();
        $message->setHtmlBody('<b>Hello</b>');
        $this->assertEquals('{"HtmlBody":"<b>Hello</b>"}', json_encode($message, JSON_UNESCAPED_SLASHES));
    }

    /**
     * @covers Postmark\Message::setFrom
     */
    public function testSetFrom()
    {
        $message = new Message();
        $message->setFrom('foo@example.org');
        $this->assertEquals('{"From":"foo@example.org"}', json_encode($message));

        $message->setFrom('foo@example.org', 'Foo Last');
        $this->assertEquals('{"From":"Foo Last <foo@example.org>"}', json_encode($message));

        $message->setFrom('foo@example.org', 'Last, Foo');
        $this->assertEquals('{"From":"\"Last, Foo\" <foo@example.org>"}', json_encode($message));
    }

    /**
     * @covers Postmark\Message::trackOpens
     */
    public function testTrackOpens()
    {
        $message = new Message();
        $message->trackOpens();
        $this->assertEquals('{"TrackOpens":true}', json_encode($message));
    }

    public function testPostmarkExamples()
    {
        $example1 = '{"From":"sender@example.com","To":"receiver@example.com","Cc":"copied@example.com","Bcc":"blank-copied@example.com","Subject":"Test","Tag":"Invitation","HtmlBody":"<b>Hello</b>","TextBody":"Hello","ReplyTo":"reply@example.com","Headers":[{"Name":"CUSTOM-HEADER","Value":"value"}]}';

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
        $this->assertJsonStringEqualsJsonString($example1, json_encode($message, JSON_UNESCAPED_SLASHES));

        $example2 = '{"From":"sender@example.com","To":"receiver@example.com","Subject":"Test","HtmlBody":"<b>Hello</b>","TextBody":"Hello","Headers":[{"Name":"CUSTOM-HEADER","Value":"value"}],"Attachments":[{"Name":"readme.txt","Content":"dGVzdCBjb250ZW50","ContentType":"text/plain"},{"Name":"report.pdf","Content":"dGVzdCBjb250ZW50","ContentType":"application/octet-stream"}]}';

        $message = new Message();
        $message->setFrom('sender@example.com');
        $message->addTo('receiver@example.com');
        $message->setSubject('Test');
        $message->setHtmlBody('<b>Hello</b>');
        $message->setTextBody('Hello');
        $message->addHeader('CUSTOM-HEADER', 'value');
        $message->addAttachment('readme.txt', 'test content', 'text/plain');
        $message->addAttachment('report.pdf', 'test content', 'application/octet-stream');
        $this->assertJsonStringEqualsJsonString($example2, json_encode($message, JSON_UNESCAPED_SLASHES));
    }

    public function testPostmarkBatchExamples()
    {
        $example1 = '[{"From":"sender@example.com","To":"receiver1@example.com","Subject":"Postmark test #1","HtmlBody":"<html><body><strong>Hello</strong> dear Postmark user.</body></html>"},{"From":"sender@example.com","To":"receiver2@example.com","Subject":"Postmark test #2","HtmlBody":"<html><body><strong>Hello</strong> dear Postmark user.</body></html>"}]';

        $message1 = new Message();
        $message1->setFrom('sender@example.com');
        $message1->addTo('receiver1@example.com');
        $message1->setSubject('Postmark test #1');
        $message1->setHtmlBody('<html><body><strong>Hello</strong> dear Postmark user.</body></html>');

        $message2 = new Message();
        $message2->setFrom('sender@example.com');
        $message2->addTo('receiver2@example.com');
        $message2->setSubject('Postmark test #2');
        $message2->setHtmlBody('<html><body><strong>Hello</strong> dear Postmark user.</body></html>');

        $messages = [$message1, $message2];
        $this->assertJsonStringEqualsJsonString($example1, json_encode($messages, JSON_UNESCAPED_SLASHES));
    }
}
