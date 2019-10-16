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
}