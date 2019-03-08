<?php declare(strict_types=1);

namespace tiFy\Partial\Partials\Sample;

use tiFy\Partial\PartialController;

class Sample extends PartialController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *      @var string $before Contenu placé avant.
     *      @var string $after Contenu placé après.
     *      @var array $attrs Attributs de balise HTML.
     *      @var array $viewer Attributs de configuration du controleur de gabarit d'affichage.
     * }
     */
    protected $attributes = [
        'before'    => '',
        'after'     => '',
        'attrs'     => [],
        'viewer'    => []
    ];

    /**
     * @inheritdoc
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * @inheritdoc
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);
    }

    /**
     * @inheritdoc
     */
    public function enqueue_scripts()
    {
        parent::enqueue_scripts();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function display()
    {
        return parent::display();
    }
}