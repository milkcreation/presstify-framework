<?php

declare(strict_types=1);

namespace tiFy\Wordpress\Partial\Drivers;

use tiFy\Partial\Drivers\ModalDriver as BaseModalDriver;

class ModalDriver extends BaseModalDriver
{
    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(
            parent::defaultParams(),
            [
                /**
                 * @var bool $in_footer Ajout automatique de la fenêtre de dialogue dans le pied de page du site.
                 */
                'in_footer' => true,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        if ($this->get('in_footer')) {
            add_action(
                (!is_admin() ? 'wp_footer' : 'admin_footer'),
                function () {
                    echo parent::render();
                },
                999999
            );

            return '';
        } else {
            return parent::render();
        }
    }
}