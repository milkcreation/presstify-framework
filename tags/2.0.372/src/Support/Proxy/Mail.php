<?php declare(strict_types=1);

namespace tiFy\Support\Proxy;

use tiFy\Contracts\Mail\Mailable as MailableContract;
use tiFy\Contracts\Mail\Mailer as MailerContract;

/**
 * @method static MailableContract create(MailableContract|array|null $params = null)
 * @method static mixed config(string|array|null $key = null, $default = null)
 * @method static void debug(MailableContract|array|null $params = null)
 * @method static mixed getDefaults(?string $key = null, $defaults = null)
 * @method static bool send(MailableContract|array|null $params = null)
 *
 * @see \tiFy\Mail\Mailer
 */
class Mail extends AbstractProxy
{
    /**
     * {@inheritDoc}
     *
     * @return mixed|object|MailerContract
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /**
     * @inheritDoc
     */
    public static function getInstanceIdentifier(): string
    {
        return 'mailer';
    }
}