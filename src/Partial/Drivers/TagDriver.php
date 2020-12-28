<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers;

use tiFy\Partial\PartialDriver;
use tiFy\Partial\PartialDriverInterface;

class TagDriver extends PartialDriver implements TagDriverInterface
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
             * @var bool $singleton Activation de balise de type singleton. ex <{tag}/>. Usage avancé, cet
             * attributon se fait automatiquement pour les balises connues.
             */
            'singleton' => false,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function parseParams(): PartialDriverInterface
    {
        parent::parseParams();

        if (in_array($this->get('tag'), $this->singleton)) {
            $this->set('singleton', true);
        }

        return $this;
    }
}