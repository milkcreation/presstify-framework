<?php declare(strict_types=1);

namespace tiFy\Mail;

use Exception;
use BadMethodCallException;
use tiFy\Contracts\Mail\Mailable;
use tiFy\Contracts\Mail\MailerDriver;
use tiFy\Contracts\Mail\MailableView as MailableViewContract;
use tiFy\View\Factory\PlatesFactory;

class MailableView extends PlatesFactory implements MailableViewContract
{
    /**
     * Instance de l'email.
     * @var Mailable|null
     */
    private $mailable;

    /**
     * Liste des méthodes héritées.
     * @var array
     */
    protected $mixins = [];

    /**
     * @inheritDoc
     */
    public function __call($name, $args)
    {
        if (in_array($name, $this->mixins)) {
            try {
                $mailable = $this->mailable();

                return $mailable->{$name}(...$args);
            } catch (Exception $e) {
                throw new BadMethodCallException(sprintf(
                    __CLASS__ . ' throws an exception during the method call [%s] with message : %s',
                    $name, $e->getMessage()
                ));
            }
        } else {
            return parent::__call($name, $args);
        }
    }

    /**
     * @inheritDoc
     */
    public function driver(): ?MailerDriver
    {
        return $this->mailable()->mailer()->getDriver();
    }

    /**
     * @inheritDoc
     */
    public function mailable(): ?Mailable
    {
        if (is_null($this->mailable)) {
            $this->mailable =  $this->engine->params('mailable');
        }

        return $this->mailable;
    }

    /**
     * @inheritDoc
     */
    public function param(string $key, $default = null)
    {
        return $this->mailable()->params($key, $default);
    }

    /**
     * @inheritDoc
     */
    public function linearizeContacts(array $contacts): array
    {
        array_walk($contacts, function (&$item) {
            $item = isset($item[1]) ? "{$item[1]} <{$item[0]}>" : "{$item[0]}";
        });

        return $contacts;
    }
}