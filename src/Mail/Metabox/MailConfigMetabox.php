<?php declare(strict_types=1);

namespace tiFy\Mail\Metabox;

use tiFy\Contracts\Mail\Mailer;
use tiFy\Metabox\MetaboxDriver;

class MailConfigMetabox extends MetaboxDriver
{
    /**
     * Instance du gestionnaire de mail.
     * @var Mailer|null
     */
    protected $mailer;

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'name'  => 'mail_config',
            'title' => __('Paramètres du message', 'tify')
        ]);
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return [
            'enabled' => [
                'activation' => true,
                'sender'     => true,
                'recipients' => true,
            ],
            'info'    => '',
            'sender'  => [
                'title'   => __('Réglages de l\'expéditeur', 'tify'),
            ],
            'recipients'  => [
                'title'   => __('Réglages des destinataires', 'tify'),
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function defaultValue()
    {
        return array_merge(['enabled' => true], parent::defaultValue() ?: []);
    }

    /**
     * Récupération de l'instance du gestionnaire de mail.
     *
     * @return Mailer|null
     */
    public function mailer(): ?Mailer
    {
        return $this->mailer;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $defaultMail = $this->mailer()->getDefaults('to');

        if(isset($defaultMail[0])) {
            if (!$this->params('sender.default.email')) {
                $this->params(['sender.default.email' => $defaultMail[0]]);
            }
            if (!$this->params('sender.default.name')) {
                $this->params(['sender.default.name' => $defaultMail[1] ?? '']);
            }
            if (!$this->params('sender.info')) {
                $this->params(['sender.info' => sprintf(__('Par défaut : %s', 'tify'), join(
                    ' - ', array_filter([$defaultMail[0], $defaultMail[1] ?? ''])
                ))]);
            }
            if (!$this->params('recipients.default.email')) {
                $this->params(['recipients.default.email' => $defaultMail[0]]);
            }
            if (!$this->params('recipients.default.name')) {
                $this->params(['recipients.default.name' => $defaultMail[1] ?? '']);
            }
            if (!$this->params('recipients.info')) {
                $this->params(['recipients.info' => sprintf(__('Par défaut : %s', 'tify'), join(
                    ' - ', array_filter([$defaultMail[0], $defaultMail[1] ?? ''])
                ))]);
            }
        }

        return parent::render();
    }

    /**
     * Définition de l'instance du gestionnaire de mail.
     *
     * @param Mailer $mailer
     *
     * @return $this
     */
    public function setMailer(Mailer $mailer): self
    {
        $this->mailer = $mailer;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->mailer()->resources('views/metabox/mail-config');
    }
}