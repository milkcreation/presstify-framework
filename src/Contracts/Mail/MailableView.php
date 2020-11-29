<?php declare(strict_types=1);

namespace tiFy\Contracts\Mail;

use tiFy\Contracts\View\PlatesFactory;

interface MailableView extends PlatesFactory
{
    /**
     * Délégation d'appel des méthodes de l'instance du gestionnaire de mail associée.
     *
     * @param string $name
     * @param array $args
     *
     * @return mixed
     */
    public function __call($name, $args);

    /**
     * Récupération de l'instance du pilote.
     *
     * @return MailerDriver|null
     */
    public function driver(): ?MailerDriver;

    /**
     * Récupération de l'instance de l'email.
     *
     * @return Mailable|null
     */
    public function mailable(): ?Mailable;

    /**
     * Récupération de paramètre.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function param(string $key, $default = null);

    /**
     * Linéarisation des informations de contact.
     *
     * @param array $contacts Informations de contact
     *
     * @return array
     */
    public function linearizeContacts(array $contacts): array;
}