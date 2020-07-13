<?php

namespace Litlife\LaravelDkim;

use Illuminate\Mail\Message;
use Swift_Message;

class Mailer extends \Illuminate\Mail\Mailer
{
	protected function createMessage()
	{
        $message = new Message($this->swift->createMessage('message'));

        // If a global from address has been specified we will set it on every message
        // instance so the developer does not have to repeat themselves every time
        // they create a new message. We'll just go ahead and push this address.
        if (! empty($this->from['address'])) {
            $message->from($this->from['address'], $this->from['name']);
        }

        // When a global reply address was specified we will set this on every message
        // instance so the developer does not have to repeat themselves every time
        // they create a new message. We will just go ahead and push this address.
        if (! empty($this->replyTo['address'])) {
            $message->replyTo($this->replyTo['address'], $this->replyTo['name']);
        }

        if (! empty($this->returnPath['address'])) {
            $message->returnPath($this->returnPath['address']);
        }

        if (in_array(strtolower(config('mail.driver')), ['smtp', 'sendmail', 'log'])) {
            if (config('mail.dkim_private_key') && file_exists(config('mail.dkim_private_key'))) {
                if (config('mail.dkim_selector') && config('mail.dkim_domain')) {
                    $message->attachDkim(config('mail.dkim_selector'), config('mail.dkim_domain'), file_get_contents(config('mail.dkim_private_key')), config('mail.dkim_passphrase'));
                }
            }
        }

		return $message;
	}

}
