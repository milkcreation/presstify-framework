<?php declare(strict_types=1);

namespace tiFy\Partial\Driver\Tag;

use tiFy\Contracts\Partial\PartialDriver as PartialDriverContract;
use tiFy\Contracts\Partial\Tag as TagContract;
use tiFy\Partial\PartialDriver;

class Tag extends PartialDriver implements TagContract
{
    /**
     * Liste des champs connu de type singleton
     * @see http://html-css-js.com/html/tags
     * @var string[]
     */
    protected $singleton = [
        'area',
        'base',
        'br',
        'col',
        'embed',
        'hr',
        'img',
        'input',
        'link',
        'meta',
        'param',
        'source',
    ];

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(parent::defaultParams(), [
            /**
             * @var string $tag Balise HTML div|span|a|... défaut div.
             */
            'tag'       => 'div',
            /**
             * @var string|callable $content Contenu de la balise HTML.
             */
            'content'   => '',
            /**
             * @var boolean $singleton Activation de balise de type singleton. ex <{tag}/>. Usage avancé, cet
             * attributon se fait automatiquement pour les balises connues.
             */
            'singleton' => false,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function parseParams(): PartialDriverContract
    {
        parent::parseParams();

        if (in_array($this->get('tag'), $this->singleton)) {
            $this->set('singleton', true);
        }

        return $this;
    }
}