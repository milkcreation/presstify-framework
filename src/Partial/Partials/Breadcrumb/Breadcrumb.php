<?php declare(strict_types=1);

namespace tiFy\Partial\Partials\Breadcrumb;

use tiFy\Contracts\Partial\Breadcrumb as BreadcrumbContract;
use tiFy\Partial\PartialFactory;

class Breadcrumb extends PartialFactory implements BreadcrumbContract
{
    /**
     * Liste des éléments contenus dans le fil d'ariane
     * @var array
     */
    protected $parts = [];

    /**
     * Indicateur de désactivation d'affichage du fil d'ariane
     * @var bool
     */
    private $disabled = false;

    /**
     * @inheritdoc
     */
    public function boot()
    {
        add_action('init', function () {
            wp_register_style(
                'PartialBreadcrumb',
                assets()->url('partial/breadcrumb/css/styles.css'),
                [],
                180122
            );
        });
    }

    /**
     * @inheritdoc
     */
    public function addPart($part)
    {
        array_push($this->parts, $part);

        return $this;
    }

    /**
     * Liste des attributs de configuration.
     *
     * @return array $attributes {
     *      @var string $before Contenu placé avant.
     *      @var string $after Contenu placé après.
     *      @var array $attrs Attributs de balise HTML.
     *      @var array $viewer Attributs de configuration du controleur de gabarit d'affichage.
     *      @var string[]|array[]|object[]|callable[] $parts Liste des élements du fil d'ariane.
     * }
     */
    public function defaults()
    {
        return [
            'before' => '',
            'after'  => '',
            'attrs'  => [],
            'viewer' => [],
            'parts'  => [],
        ];
    }

    /**
     * @inheritdoc
     */
    public function disable()
    {
        $this->disabled = true;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function display()
    {
        if ($this->disabled) :
            return '';
        endif;

        $this->set('items', $this->parsePartList());

        return parent::display();
    }

    /**
     * @inheritdoc
     */
    public function enable()
    {
        $this->disabled = false;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function enqueue_scripts()
    {
        wp_enqueue_style('PartialBreadcrumb');
    }

    /**
     * @inheritdoc
     */
    public function parsePartList()
    {
        if (!$this->parts) :
            $this->parts = (new WpQueryPart())->getList();
        endif;

        $parts = [];
        foreach($this->parts as $part) :
            $parts[] = $this->parsePart($part);
        endforeach;

        return $parts;
    }

    /**
     * @inheritdoc
     */
    public function parsePart($part)
    {
        if (is_string($part)) :
            return $part;
        elseif (is_object($part) && is_string((string) $part)) :
            return (string)$part;
        elseif (is_array($part)) :
            $defaults = [
                'class'     => 'tiFyPartial-BreadcrumbItem',
                'content'   => ''
            ];
            $part = array_merge($defaults, $part);

            return "<li class=\"{$part['class']}\">{$part['content']}</li>";
        endif;

        return '';
    }

    /**
     * @inheritdoc
     */
    public function prependPart($part)
    {
        array_unshift($this->parts, $part);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function reset()
    {
        $this->parts = [];

        return $this;
    }
}