<?php

namespace tiFy\Field\RadioCollection;

use tiFy\App\Item\AbstractAppItemController;

class RadioItem extends AbstractAppItemController
{
    /**
     * Définition de l'index de l'élément
     * @var int
     */
    static $index = 0;

    /**
     * Classe de rappel du controleur de l'application associée.
     * @var RadioCollection
     */
    protected $app;

    /**
     * Nom de qualification
     * @var int|string
     */
    protected $name = '';

    /**
     * CONSTRUCTEUR.
     *
     * @param string|int $name
     * @param array|string $attrs
     * @param RadioCollection $app
     *
     * @return void
     */
    public function __construct($name, $attrs, RadioCollection $app)
    {
        $this->name = $name;
        self::$index++;

        if (is_string($attrs)) :
            $attrs = [
                'label' => [
                    'content' => $attrs
                ],
            ];
        endif;

        parent::__construct($attrs, $app);
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'attrs'     => [],
            'label'     => [
                'before'       => '',
                'after'        => '',
                'content'      => '',
                'attrs'        => []
            ],
            'radio'  => [
                'before'  => '',
                'after'   => '',
                'attrs'   => [],
                'name'    => '',
                'value'   => $this->name,
                'checked' => null
            ]

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        if (!$this->get('attrs.id')) :
            $this->set('attrs.id', 'tiFyField-RadioCollectionItem--'. self::$index);
        endif;
        if (!$this->get('radio.name')) :
            $this->set('checkbox.name', $this->app->get('name'));
        endif;
        if (!$this->get('radio.checked')) :
            $this->set('checkbox.checked', $this->app->get('checked'));
        endif;
        if (!$this->get('radio.attrs.id')) :
            $this->set('radio.attrs.id', 'tiFyField-RadioCollectionItemInput--'. self::$index);
        endif;
        if (!$this->get('radio.attrs.class')) :
            $this->set('radio.attrs.class', 'tiFyField-RadioCollectionItemInput');
        endif;

        if (!$this->get('label.attrs.id')) :
            $this->set('label.attrs.id', 'tiFyField-RadioCollectionItemLabel--'. self::$index);
        endif;
        if (!$this->get('label.attrs.class')) :
            $this->set('label.attrs.class', 'tiFyField-RadioCollectionItemLabel');
        endif;
        if (!$this->get('label.attrs.for')) :
            $this->set('label.attrs.for', 'tiFyField-RadioCollectionItemInput--'. self::$index);
        endif;
    }

    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->view('item', $this->all());
    }
}