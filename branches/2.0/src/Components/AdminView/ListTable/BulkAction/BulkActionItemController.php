<?php

namespace tiFy\Components\AdminView\ListTable\BulkAction;

use tiFy\AdminView\AdminViewControllerInterface;
use tiFy\Apps\Attributes\AbstractAttributesController;

class BulkActionItemController extends AbstractAttributesController implements BulkActionItemInterface
{
    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * Classe de rappel de la vue associée.
     * @var AdminViewControllerInterface
     */
    protected $app;

    /**
     * Liste des attributs de configuration.
     * @return array
     */
    protected $attributes = [
        'value'   => '',
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
     * @param AdminViewControllerInterface $app Classe de rappel de la vue associée.
     *
     * @return void
     */
    public function __construct($name, $attrs = [], AdminViewControllerInterface $app)
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

        if (!$this->get('content')) :
            $this->set('content', $this->name);
        endif;
    }
}