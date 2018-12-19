<?php

namespace tiFy\Mail;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Mail\Adapter\PhpMailer as AdapterPhpMailer;
use PHPMailer;

class MailServiceProvider extends AppServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->singleton('mailer', function () { return new Mailer(); })->build();

        $this->app->bind(
            'mailer.library', function () {
                switch(config('mail.library')) :
                    default :
                        $adapter = new AdapterPhpMailer(new PHPMailer(true));
                        break;
                endswitch;

                return $adapter;
            }
        );

        $this->app->bind(
            'mailer.message',
            function ($mailer) {
                return new Message($mailer);
            }
        );

        $this->app->singleton('mail.queue', function () { return new MailQueue(); })->build();
    }
}