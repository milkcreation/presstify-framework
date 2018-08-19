<?php

namespace tiFy\Components\Field\CheckboxCollection;

use tiFy\Apps\Item\AbstractAppItemController;

class CheckboxItem extends AbstractAppItemController
{
    /**
     * Définition de l'index de l'élément
     * @var int
     */
    static $index = 0;

    /**
     * Classe de rappel du controleur de l'application associée.
     * @var CheckboxCollection
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
     * @param CheckboxCollection $app
     *
     * @return void
     */
    public function __construct($name, $attrs, CheckboxCollection $app)
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
            'checkbox'  => [
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
            $this->set('attrs.id', 'tiFyField-CheckboxCollectionItem--'. self::$index);
        endif;
        if (!$this->get('checkbox.name')) :
            $this->set('checkbox.name', $this->app->get('name'));
        endif;
        if (!$this->get('checkbox.checked')) :
            $this->set('checkbox.checked', $this->app->get('checked'));
        endif;
        if (!$this->get('checkbox.attrs.id')) :
            $this->set('checkbox.attrs.id', 'tiFyField-CheckboxCollectionItemInput--'. self::$index);
        endif;
        if (!$this->get('checkbox.attrs.class')) :
            $this->set('checkbox.attrs.class', 'tiFyField-CheckboxCollectionItemInput');
        endif;

        if (!$this->get('label.attrs.id')) :
            $this->set('label.attrs.id', 'tiFyField-CheckboxCollectionItemLabel--'. self::$index);
        endif;
        if (!$this->get('label.attrs.class')) :
            $this->set('label.attrs.class', 'tiFyField-CheckboxCollectionItemLabel');
        endif;
        if (!$this->get('label.attrs.for')) :
            $this->set('label.attrs.for', 'tiFyField-CheckboxCollectionItemInput--'. self::$index);
        endif;
    }

    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->app->appTemplateRender('item', $this->all());
    }
}