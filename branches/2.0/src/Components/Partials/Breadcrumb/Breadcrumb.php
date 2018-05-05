<?php

/**
 * @name Breadcrumb
 * @desc Controleur d'affichage de fil d'ariane
 * @package presstiFy
 * @namespace \tiFy\Components\Partials\Breadcrumb
 * @version 1.1
 * @subpackage Components
 * @since 1.2.571
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Components\Partials\Breadcrumb;

use tiFy\Partial\AbstractFactory;

/**
 * @param array $attrs {
 *      Liste des attributs de configuration
 *
 *      @var string $id Identifiant de qualification du controleur d'affichage.
 *      @var string $container_id ID HTML du conteneur de l'élément.
 *      @var string $container_class Classes HTML du conteneur de l'élément.
 *      @var string[]|array[]|object[]|callable[] $parts Liste des élements du fil d'ariane.
 * }
 */
class Breadcrumb extends AbstractFactory
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
     * Traitement des attributs de configuration
     *
     * @param array $attrs Liste des attributs de configuration
     *
     * @return array
     */
    final protected function parse($attrs = [])
    {
        $defaults = [
            'container_id'    => 'tiFyPartial-breadcrumb--' . $this->getIndex(),
            'container_class' => '',
            'parts'           => []
        ];
        $attrs = array_merge($defaults, $attrs);

        $class = "tiFyPartial-breadcrumb tiFyPartial-breadcrumb--" . $this->getId();
        $attrs['container_class'] = $attrs['container_class']
            ? $class . " " . $attrs['container_class']
            : $class;

        return $attrs;
    }

    /**
     * Initialisation globale.
     *
     * @return void
     */
    final public function init()
    {
        \wp_register_style(
            'tiFyPartialBreadcrumb',
            $this->appAbsUrl() . '/assets/Breadcrumb/css/styles.css',
            [],
            180122
        );
    }

    /**
     * Mise en file des scripts
     *
     * @return void
     */
    final public function enqueue_scripts()
    {
        \wp_enqueue_style('tiFyPartialBreadcrumb');
    }

    /**
     * Récupération de la liste des éléments contenus dans le fil d'ariane
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
     * Traitement d'un élément de contenu du fil d'arianne
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
        elseif (is_callable($part)) :

        elseif (is_array($part)) :
            $defaults = [
                'class'     => '',
                'content'   => ''
            ];
            $part = array_merge($defaults, $part);

            return "<li class=\"{$part['class']}\">{$part['content']}</li>";
        endif;

        return '';
    }

    /**
     * Ajout d'un élément de contenu au fil d'arianne
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
     * Ajout d'un élément de contenu en début de chaîne du fil d'arianne
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
     * Supprime l'ensemble des éléments de contenu prédéfinis
     *
     * @return $this
     */
    public function resetParts()
    {
        $this->parts = [];

        return $this;
    }

    /**
     * Désactivation de l'affichage
     *
     * @return $this
     */
    public function disable()
    {
        $this->disabled = true;

        return $this;
    }

    /**
     * Activation de l'affichage
     *
     * @return $this
     */
    public function enable()
    {
        $this->disabled = false;

        return $this;
    }

    /**
     * Affichage
     *
     * @return string
     */
    final protected function display()
    {
        if ($this->disabled) :
            return '';
        endif;

        // Définition des arguments de template
        $id = $this->getId();
        $index = $this->getIndex();
        $container_id = $this->get('container_id');
        $container_class = $this->get('container_class', '');
        $parts = $this->parsePartList();

        // Récupération du template d'affichage
        ob_start();
        //self::tFyAppGetTemplatePart('breadcrumb', $this->getId(), compact('id', 'index', 'container_id', 'container_class', 'parts'));

        return ob_get_clean();
    }
}