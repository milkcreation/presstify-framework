<?php declare(strict_types=1);

namespace tiFy\Form\Addon\Mailer;

class MailerOptionsNotification extends AbstractMetaboxDriver
{
    /**
     * @inheritDoc
     */
    public function content(): string
    {
        $this->set([
            'option_names' => $this->optionNames,
            'option_values' => [
                'notification' => get_option($this->optionNames['notification'], 'off') ?: 'off',
                'recipients'   => get_option($this->optionNames['recipients']) ?: [],
            ]
        ]);

        return (string) $this->viewer('notification', $this->all());
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'title' => __('Notification', 'tify')
        ]);
    }

    /**
     * Vérification du format de l'email du destinataire de notification
     *
     * @param array $recipients Attributs des destinataires
     *
     * @return array

    public function sanitize_recipients($recipients)
    {
        if ($recipients) :
            foreach ($recipients as $recipient => $recip) :
                if (empty($recip['email'])) :
                    add_settings_error(
                        $this->getObjectName(),
                        $recipient . '-email_empty',
                        __(
                            'L\'email du destinataire des messages de notification ne peut être vide',
                            'theme'
                        )
                    );
                elseif (!is_email($recip['email'])) :
                    add_settings_error(
                        $this->getObjectName(),
                        $recipient . '-email_format',
                        __(
                            'Le format de l\'email du destinataire des messages de notification #%d ' .
                            'n\'est pas valide',
                            'theme'
                        )
                    );
                endif;
            endforeach;
        endif;

        return $recipients;
    }    */

    /**
     * {@inheritdoc}
     */
    public function settings()
    {
        return [
            $this->optionNames['notification'],
            $this->optionNames['recipients'] => [
                'sanitize_callback' => [$this, 'sanitize_recipients']
            ]
        ];
    }
}