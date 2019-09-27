<?php declare(strict_types=1);

namespace tiFy\Form\Addon\Mailer;

class MailerOptionsConfirmation extends AbstractMetaboxDriver
{
    /**
     * @inheritDoc
     */
    public function content(): string
    {
        $this->set([
            'option_names' => $this->optionNames,
            'option_values' => [
                'confirmation' => get_option($this->optionNames['confirmation'], 'off') ?: 'off',
                'sender'       => array_merge(
                    [
                        'email' => get_option('admin_email'),
                        'name'  => ''
                    ],
                    get_option($this->optionNames['sender']) ?: []
                )
            ]
        ]);

        return (string) $this->viewer('confirmation', $this->all());
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'title' => __('Confirmation', 'tify')
        ]);
    }

    /**
     * Vérification du format de l'email de l'expéditeur
     *
     * @param array $sender Attributs de l'expéditeur
     *
     * @return array

    public function sanitize_sender($sender)
    {
        if (empty($sender['email'])) :
            add_settings_error(
                $this->getObjectName(),
                'sender-email_empty',
                sprintf(
                    __('L\'email "%s" ne peut être vide', 'theme'),
                    __('Expéditeur du message de confirmation de reception', 'theme')
                )
            );
        elseif (!is_email($sender['email'])) :
            add_settings_error(
                $this->getObjectName(),
                'sender-email_format',
                sprintf(
                    __('Le format de l\'email "%s" n\'est pas valide', 'theme'),
                    __('Expéditeur du message de confirmation de reception', 'theme')
                )
            );
        endif;

        return $sender;
    }  */

    /**
     * @inheritDoc
     */
    public function settings()
    {
        return [
            $this->optionNames['confirmation'],
            $this->optionNames['sender']     => [
                'sanitize_callback' => [$this, 'sanitize_sender']
            ]
        ];
    }
}