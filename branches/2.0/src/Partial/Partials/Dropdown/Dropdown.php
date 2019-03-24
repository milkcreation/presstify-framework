<?php declare(strict_types=1);

namespace tiFy\Partial\Partials\Dropdown;

use tiFy\Contracts\Partial\Dropdown as DropdownContract;
use tiFy\Contracts\Partial\DropdownItems as DropdownItemsContract;
use tiFy\Partial\PartialFactory;

class Dropdown extends PartialFactory implements DropdownContract
{
    /**
     * @inheritdoc
     */
    public function boot()
    {
        add_action('init', function () {
            wp_register_style(
                'PartialDropdown',
                assets()->url('partial/dropdown/css/styles.css'),
                [],
                181221
            );
            wp_register_script(
                'PartialDropdown',
                assets()->url('partial/dropdown/js/scripts.js'),
                ['jquery-ui-widget'],
                181221,
                true
            );
        });
    }

    /**
     * Liste des attributs de configuration.
     *
     * @return array $attributes {
     *      @var string $before Contenu placé avant.
     *      @var string $after Contenu placé après.
     *      @var array $attrs Attributs de balise HTML.
     *      @var array $viewer Attributs de configuration du controleur de gabarit d'affichage.
     * }
     */
    public function defaults()
    {
        return [
            'before'    => '',
            'after'     => '',
            'attrs'     => [],
            'viewer'    => [],
            'button'    => '',
            'items'     => [],
            'open'      => false,
            'trigger'   => false
        ];
    }

    /**
     * @inheritdoc
     */
    public function display()
    {
        return parent::display();
    }

    /**
     * @inheritdoc
     */
    public function enqueue_scripts()
    {
        wp_enqueue_style('PartialDropdown');
        wp_enqueue_script('PartialDropdown');
    }

    /**
     * @inheritdoc
     */
    public function parse()
    {
        parent::parse();

        $this->set('attrs.class', sprintf($this->get('attrs.class', '%s'), 'PartialDropdown'));
        $this->set('attrs.data-control', 'dropdown');
        $this->set('attrs.data-id', $this->getId());

        $classes = [
            'button'    => 'PartialDropdown-button',
            'listItems' => 'PartialDropdown-items',
            'item'      => 'PartialDropdown-item'
        ];
        foreach($classes as $key => &$class) :
            $class = sprintf($this->get("classes.{$key}", '%s'), $class);
        endforeach;
        $this->set('classes', $classes);

        $items = $this->get('items', []);

        if (!$items instanceof DropdownItemsContract) :
            $items = new DropdownItems($items);
        endif;
        $this->set('items', $items->setPartial($this));

        $this->set('attrs.data-options', [
            'classes' => $this->get('classes', []),
            'open'    => $this->get('open'),
            'trigger' => $this->get('trigger'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function parseDefaults()
    {
        foreach($this->get('view', []) as $key => $value) :
            $this->viewer()->set($key, $value);
        endforeach;
    }
}