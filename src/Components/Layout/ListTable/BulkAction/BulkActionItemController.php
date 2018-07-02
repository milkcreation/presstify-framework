<?php

namespace tiFy\Components\Layout\ListTable\BulkAction;

use tiFy\Kernel\Layout\LayoutControllerInterface;
use tiFy\Apps\Attributes\AbstractAttributesIterator;

class BulkActionItemController extends AbstractAttributesIterator implements BulkActionItemInterface
{
    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * Classe de rappel de la vue associée.
     * @var LayoutControllerInterface
     */
    protected $app;

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
     * @param LayoutControllerInterface $app Classe de rappel de la vue associée.
     *
     * @return void
     */
    public function __construct($name, $attrs = [], LayoutControllerInterface $app)
    {
        $this->name = $name;

        parent::__construct($attrs, $app);
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