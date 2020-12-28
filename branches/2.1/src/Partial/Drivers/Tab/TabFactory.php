<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers\Tab;

use Closure;
use Exception;
use tiFy\Support\HtmlAttrs;
use tiFy\Support\ParamsBag;

class TabFactory extends ParamsBag implements TabFactoryInterface
{
    /**
     * Indicateur de chargement
     * @var bool
     */
    private $booted = false;

    /**
     * Indicateur d'initialisation
     * @var bool
     */
    private $built = false;

    /**
     * Instance du gestionnaire d'éléments.
     * @var TabCollectionInterface
     */
    protected $collection;

    /**
     * Identifiant de qualification.
     * @var string
     */
    private $id;

    /**
     * Identifiant d'indexation.
     * @var int
     */
    private $index = 0;

    /**
     * Niveau de profondeur dans l'interface d'affichage.
     * @var int
     */
    protected $depth = 0;

    /**
     * Instance de l'élément parent.
     * @var TabFactoryInterface|null
     */
    protected $parent;

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * Génération des identifiants de qualification.
     *
     * @return void
     */
    private function generateIds(): void
    {
        $this->index = $this->collection()->getIncreasedItemIdx();
        $this->id = "tab-{$this->collection()->tabManager()->getIndex()}--{$this->index}";

        $name = $this->get('name', null);
        if (!$name || !is_string($name)) {
            $this->set('name', $this->id);
        }
    }

    /**
     * @inheritDoc
     */
    public function boot(): TabFactoryInterface
    {
        if (!$this->booted) {
            $this->parse();

            $this->booted = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function build(): TabFactoryInterface
    {
        if (!$this->built) {
            try {
                $this->generateIds();
            } catch (Exception $e) {
                throw new Exception('Tab factory generation id failed');
            }

            $this->built = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function collection(): TabCollectionInterface
    {
        return $this->collection;
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return [
            'content'  => '',
            'name'     => '',
            'parent'   => null,
            'position' => null,
            'title'    => '',
        ];
    }

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * @inheritDoc
     */
    public function getChildren(): iterable
    {
        return $this->collection()->getGrouped($this->getName());
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        $content = $this->get('content');
        return $content instanceof Closure ? call_user_func($content) : (string)$content;
    }

    /**
     * @inheritDoc
     */
    public function getContentAttrs(bool $linearized = true): string
    {
        $attrs = [
            'id'           => $this->getId(),
            'class'        => 'Tab-contentPane',
            'data-name'    => $this->getName(),
            'data-control' => 'tab.content.pane'
        ];
        return HtmlAttrs::createFromAttrs($attrs, $linearized);
    }

    /**
     * @inheritDoc
     */
    public function getDepth(): int
    {
        return $this->depth;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->get('name');
    }

    /**
     * @inheritDoc
     */
    public function getNavAttrs(bool $linearized = true): string
    {
        $attrs = [
            'class'         => 'Tab-navLink',
            'data-control'  => 'tab.nav.link',
            'data-name'     => $this->getName(),
            'href'          => "#{$this->getId()}",
        ];
        return HtmlAttrs::createFromAttrs($attrs, $linearized);
    }

    /**
     * @inheritDoc
     */
    public function getParent(): ?TabFactoryInterface
    {
        if (is_null($this->parent)) {
            if ($name = $this->get('parent', null)) {
                $this->parent = $this->collection()->get($name) ?: false;
            } else {
                $this->parent = false;
            }
        }
        return $this->parent ?: null;
    }

    /**
     * @inheritDoc
     */
    public function getParentName(): string
    {
        return ($parent = $this->getParent()) instanceof TabFactoryInterface ? $parent->getName() : '';
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return (string)$this->get('title');
    }

    /**
     * @inheritDoc
     */
    public function isBooted(): bool
    {
        return $this->booted;
    }

    /**
     * @inheritDoc
     */
    public function isBuilt(): bool
    {
        return $this->built;
    }

    /**
     * @inheritDoc
     */
    public function setCollection(TabCollectionInterface $collection): TabFactoryInterface
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDepth(int $depth = 0): TabFactoryInterface
    {
        $this->depth = $depth;

        return $this;
    }
}