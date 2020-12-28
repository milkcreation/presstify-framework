<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers\Sidebar;

use tiFy\Support\Callback;
use tiFy\Support\ParamsBag;

class SidebarItem extends ParamsBag
{
    /**
     * Nom de qualification de l'élément.
     * @var string
     */
    protected $name = '';

    /**
     * @param string $name Nom de qualification de l'élément.
     * @param array $attrs Liste des attributs.
     */
    public function __construct(string $name, array $attrs = [])
    {
        $this->name = $name;
        $this->set($attrs)->parse();
    }

    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString(): string
    {
        $content = $this->get('content');

        return Callback::make($content) ?: $content;
    }

    /**
     * {@inheritdoc}
     */
    public function defaults(): array
    {
        return [
            'name'     => $this->name,
            'attrs'    => [],
            'content'  => '',
            'position' => 0,
        ];
    }

    /**
     * @inheritDoc
     */
    public function parse($attrs = []): self
    {
        parent::parse();

        $this->set(
            'attrs.class',
            trim(
                sprintf("Sidebar-item Sidebar-item--{$this->name} %s", $this->get('attrs.class') ?: '')
            )
        );

        return $this;
    }
}
