<?php declare(strict_types=1);

namespace tiFy\Contracts\Mail;

use DateTime;

interface MailerQueue
{
    /**
     * Ajout d'un élément dans la file d'attente
     *
     * @param Mailable $mailable Instance de l'email.
     * @param string|DateTime $date Date de programmation d'envoi du mail au format timestamp.
     * @param array $params Paramètres complémentaires.
     *
     * @return int
     */
    public function add(Mailable $mailable, $date = 'now', array $params = []);

    /**
     * Définition du gestionnaire de mails.
     *
     * @param Mailer $mailer
     *
     * @return static
     */
    public function setMailer(Mailer $mailer): MailerQueue;
}