<?php

declare(strict_types=1);

namespace tiFy\Mail;

use tiFy\Contracts\Mail\Mailer as MailerContract;
use tiFy\Contracts\Mail\Mailable as MailableContract;
use tiFy\Contracts\Mail\MailerDriver as MailerDriverContract;
use tiFy\Contracts\Mail\MailerQueue as MailerQueueContract;
use tiFy\Container\ServiceProvider;
use tiFy\Mail\Driver\PhpMailerDriver;
use tiFy\Mail\Metabox\MailConfigMetabox;
use tiFy\Metabox\Contracts\MetaboxContract;
use PHPMailer\PHPMailer\PHPMailer;
use tiFy\Support\Proxy\View;

class MailServiceProvider extends ServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * @var string[]
     */
    protected $provides = [
        'mailer',
        'mail.driver',
        'mail.mailable.view-engine',
        'mail.mailable',
        MailConfigMetabox::class,
        'mail.queue',
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(
            'mailer',
            function (): MailerContract {
                return (new Mailer(config('mail', []), $this->getContainer()));
            }
        );

        $this->getContainer()->add(
            'mail.driver',
            function (): MailerDriverContract {
                $driver = config('mail.driver', null);

                if (!$driver instanceof MailerDriverContract) {
                    $driver = new PhpMailerDriver(new PHPMailer(env('APP_DEBUG')));
                }

                return $driver;
            }
        );

        $this->getContainer()->share(
            'mail.mailable',
            function (): MailableContract {
                return (new Mailable())->setMailer($this->getContainer()->get('mailer'));
            }
        );

        $this->getContainer()->share(
            'mail.queue',
            function (): MailerQueueContract {
                return (new MailerQueue())->setMailer($this->getContainer()->get('mailer'));
            }
        );

        $this->getContainer()->add(
            MailConfigMetabox::class,
            function () {
                return new MailConfigMetabox(
                    $this->getContainer()->get('mailer'),
                    $this->getContainer()->get(MetaboxContract::class)
                );
            }
        );

        $this->getContainer()->add(
            'mail.mailable.view-engine',
            function () {
                return View::getPlatesEngine();
            }
        );
    }
}