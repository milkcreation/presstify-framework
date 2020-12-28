<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers\Accordion;

use tiFy\Support\ParamsBag;

class AccordionItem extends ParamsBag implements AccordionItemInterface
{
    /**
     * Indicateur d'initialisation.
     * @var bool
     */
    private $built = false;

    /**
     * Identifiant de qualification de l'élément.
     * @var string|int
     */
    protected $id = '';

    /**
     * Instance du gestionnaire d'affichage de la liste des éléments.
     * @var AccordionCollectionInterface
     */
    protected $collection;

    /**
     * @param string|int $id Identifiant de qualification de l'élément.
     * @param string|array $attrs Liste des attributs de configuration.
     */
    public function __construct($id, $attrs)
    {
        $this->id = $id;

        if (is_string($attrs)) {
            $attrs = ['render' => $attrs];
        }

        $this->set($attrs);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * @inheritDoc
     */
    public function build(): AccordionItemInterface
    {
        if(!$this->built) {
            $this->parse();

            $this->built = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return [
            'parent'  => null,
            'attrs'   => [],
            'depth'   => 0,
            'open'    => false,
            'render'  => '',
        ];
    }

    /**
     * @inheritDoc
     */
    public function getDepth(): int
    {
        return $this->get('depth') ? : 0;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getParent(): ?string
    {
        return ($parent = $this->get('parent', null)) ? (string) $parent : null;
    }

    /**
     * @inheritDoc
     */
    public function isOpened(): bool
    {
        return (bool)$this->get('open');
    }

    /**
     * @inheritDoc
     */
    public function parse()
    {
        parent::parse();

        $this->set([
            'attrs.class'        => 'Accordion-itemContent',
            'attrs.data-control' => 'accordion.item.content',
        ]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return $this->get('render');
    }

    /**
     * @inheritDoc
     */
    public function setDepth(int $depth): AccordionItemInterface
    {
        $this->set('depth', $depth);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setOpened(bool $open = true): AccordionItemInterface
    {
        $this->set('open', $open);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setCollection(AccordionCollectionInterface $collection): AccordionItemInterface
    {
        $this->collection = $collection;

        return $this;
    }
}