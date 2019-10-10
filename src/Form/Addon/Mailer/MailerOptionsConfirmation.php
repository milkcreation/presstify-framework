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
}