<?php

namespace tiFy\Partial\Breadcrumb;

use tiFy\Partial\AbstractPartialItem;
use tiFy\Kernel\Tools;

class Breadcrumb extends AbstractPartialItem
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *      @var string $attrs Liste des attributs HTML de la balise HTML du conteneur de l'élément.
     *      @var string[]|array[]|object[]|callable[] $parts Liste des élements du fil d'ariane.
     * }
     */
    protected $attributes = [
        'attrs'           => [],
        'parts'           => []
    ];

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
     * {@inheritdoc}
     */
    public function boot()
    {
        add_action(
            'init',
            function () {
                \wp_register_style(
                    'PartialBreadcrumb',
                    \assets()->url('/partial/breadcrumb/css/styles.css'),
                    [],
                    180122
                );
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function enqueue_scripts()
    {
        \wp_enqueue_style('PartialBreadcrumb');
    }

    /**
     * Récupération de la liste des éléments contenus dans le fil d'ariane.
     *
     * @return string[]
     */
    private function parsePartList()
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
     * Traitement d'un élément de contenu du fil d'arianne.
     *
     * @param string|array|object|callable $part Element du fil d'ariane.
     *
     * @return string
     */
    private function parsePart($part)
    {
        if (is_string($part)) :
            return $part;
        elseif (is_object($part) && is_string((string) $part)) :
            return (string)$part;
        elseif ($this->isCallable($part)) :

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
     * Ajout d'un élément de contenu au fil d'arianne.
     *
     * @param string|array|object|callable $part Element du fil d'ariane.
     *
     * @return $this
     */
    final public function addPart($part)
    {
        array_push($this->parts, $part);

        return $this;
    }

    /**
     * Ajout d'un élément de contenu en début de chaîne du fil d'arianne.
     *
     * @param string|array|object|callable $part Element du fil d'ariane.
     *
     * @return $this
     */
    final public function prependPart($part)
    {
        array_unshift($this->parts, $part);

        return $this;
    }

    /**
     * Supprime l'ensemble des éléments de contenu prédéfinis.
     *
     * @return $this
     */
    public function resetParts()
    {
        $this->parts = [];

        return $this;
    }

    /**
     * Désactivation de l'affichage.
     *
     * @return $this
     */
    public function disable()
    {
        $this->disabled = true;

        return $this;
    }

    /**
     * Activation de l'affichage.
     *
     * @return $this
     */
    public function enable()
    {
        $this->disabled = false;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function display()
    {
        if ($this->disabled) :
            return '';
        endif;

        $this->set('items', $this->parsePartList());

        return $this->view()->render(
            'breadcrumb',
            $this->all()
        );
    }
}