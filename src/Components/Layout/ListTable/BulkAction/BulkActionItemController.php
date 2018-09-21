<?php

namespace tiFy\Components\Layout\ListTable\BulkAction;

use tiFy\App\Layout\LayoutInterface;
use tiFy\App\Item\AbstractAppItemIterator;

class BulkActionItemController extends AbstractAppItemIterator implements BulkActionItemInterface
{
    /**
     * Classe de rappel de la vue associée.
     * @var LayoutInterface
     */
    protected $app;

    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * Liste des attributs de configuration.
     * @return array
     */
    protected $attributes = [
        'value'   => null,
        'content' => '',
        'group'   => false,
        'attrs'   => [],
        'parent'  => ''
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration personnalisés.
     * @param LayoutInterface $app Classe de rappel de la vue associée.
     *
     * @return void
     */
    public function __construct($name, $attrs = [], LayoutInterface $app)
    {
        $this->name = $name;

        parent::__construct($attrs, $app);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        if ($this->get('value', null) === null) :
            $this->set('value', $this->name);
        endif;

        if (!$this->get('content')) :
            $this->set('content', $this->name);
        endif;
    }
}