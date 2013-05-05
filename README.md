Postmark-PHP - Wrapper for PHP 5.4+
===================================

Postmark is an easy to integrate email service. This wrapper allows 
you use to use the api in your php applications.

Usage
-----

```php
<?php

use Postmark\Message;
use Postmark\Mail;

// create a message
$message = new Message();
$message->setFrom('sender@example.com');
$message->addTo('receiver@example.com');
$message->setSubject('Test');
$message->setTextBody('Hello');

// send message
$mail = new Mail('POSTMARK_API_TEST');
$mail->send($message);
```

Batch usages
```php
<?php

use Postmark\Message;
use Postmark\Mail;

// create a message
$message = new Message();
$message->setFrom('sender@example.com');
$message->addTo('receiver@example.com');
$message->setSubject('Test');
$message->setTextBody('Hello');

// create an array of messages you want to send
$messages = array($message, $message);

// send message
$mail = new Mail('POSTMARK_API_TEST');
$mail->batchSend($messages);
```

Author
------

John Piasetzki - <john@piasetzki.name> - <http://twitter.com/jpiasetz><br />

License
-------

Postmark-PHP is licensed under the MIT License - see the `LICENSE` file for details
