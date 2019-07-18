<?php declare(strict_types=1);

namespace tiFy\Partial\Partials\CurtainMenu;

use tiFy\Contracts\Partial\{CurtainMenuItem as CurtainMenuItemContract, CurtainMenuItems};
use tiFy\Support\{HtmlAttrs, ParamsBag};

class CurtainMenuItem extends ParamsBag implements CurtainMenuItemContract
{
    /**
     * Nom de qualification de l'élément.
     * @var string
     */
    protected $name = '';

    /**
     * Instance du gestionnaire d'éléments.
     * @var CurtainMenuItems
     */
    protected $manager;

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification de l'élément.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return void
     */
    public function __construct(string $name, array $attrs = [])
    {
        $this->name = $name;

        $this->set($attrs);
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return [
            'attrs'      => [],
            'back'       => [],
            'depth'      => 0,
            'parent'     => null,
            'selected'   => false,
            'title'      => [],
        ];
    }

    /**
     * @inheritDoc
     */
    public function hasParent(): bool
    {
        return $this->getParent() !== null;
    }

    /**
     * @inheritDoc
     */
    public function getAttrs(bool $linearized = true)
    {
        return HtmlAttrs::createFromAttrs($this->get('attrs', []), $linearized);
    }

    /**
     * @inheritDoc
     */
    public function getBack(): array
    {
        return $this->get('back', []);
    }

    /**
     * @inheritDoc
     */
    public function getChilds(): array
    {
        return $this->manager->getParentItems($this->getName());
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        return $this->get('content', '');
    }

    /**
     * @inheritDoc
     */
    public function getDepth(): int
    {
        return (int)$this->get('depth', 0);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getParent(): ?CurtainMenuItemContract
    {
        return ($name = $this->getParentName()) && ($parent = $this->manager->get($name)) ? $parent : null;
    }

    /**
     * @inheritDoc
     */
    public function getParentName(): ?string
    {
        return $this->get('parent');
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): array
    {
        return $this->get('title', []);
    }

    /**
     * @inheritDoc
     */
    public function isSelected(): bool
    {
        return (bool)$this->get('selected', false);
    }

    /**
     * @inheritDoc
     */
    public function setDepth(int $depth): CurtainMenuItemContract
    {
        $this->set('depth', $depth);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setManager(CurtainMenuItems $manager): CurtainMenuItemContract
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setSelected($selected = false): CurtainMenuItemContract
    {
        $this->set('selected', $selected);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parse(): CurtainMenuItemContract
    {
        parent::parse();

        $this->set('attrs.data-control', 'curtain-menu.item');
        $this->set('attrs.class', 'CurtainMenu-item');

        $back = $this->get('back', []);
        if (is_string($back)) {
            $back = ['content' => $back];
        }
        $this->set('back', array_merge([
            'attrs' => [
                'class' => 'CurtainMenu-itemBack',
                'href'  => '#'
            ],
            'content' => __('Retour', 'tify'),
            'tag' => 'a',
        ], $back));
        $this->set('back.attrs.data-control', 'curtain-menu.back');

        $title = $this->get('title', []);
        if (is_string($title)) {
            $title = ['content' => $title];
        }
        $this->set('title', array_merge([
            'attrs' => [
                'class' => 'CurtainMenu-itemTitle'
            ],
            'content' => $this->getName(),
            'tag' => 'h3',
        ], $title));
        $this->set('title.attrs.data-control', 'curtain-menu.title');

        return $this;
    }
}