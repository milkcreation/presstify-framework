<?php

declare(strict_types=1);

namespace tiFy\Support\Proxy;

use Pollen\Mail\MailableInterface;
use Pollen\Mail\MailerDriverInterface;
use Pollen\Mail\MailManagerInterface;

/**
 * @method static void debug(MailableInterface|array|null $mailableDef = null)
 * @method static mixed defaults(array|string|null $key = null, $default = null)
 * @method static MailableInterface getMailable(?string $name = null)
 * @method static MailerDriverInterface getMailer()
 * @method static bool hasMailable()
 * @method static bool send(MailableInterface|array|null $mailableDef = null)
 * @method static MailManagerInterface setDefaults(array $attrs)
 * @method static MailManagerInterface setMailable(MailableInterface|array|null $mailableDef = null)
 * @method static MailManagerInterface setMailerConfigCallback(callable $mailerConfigCallback)
 */
class Mail extends AbstractProxy
{
    /**
     * {@inheritDoc}
     *
     * @return MailManagerInterface
     */
    public static function getInstance(): MailManagerInterface
    {
        return parent::getInstance();
    }

    /**
     * @inheritDoc
     */
    public static function getInstanceIdentifier(): string
    {
        return MailManagerInterface::class;
    }
}