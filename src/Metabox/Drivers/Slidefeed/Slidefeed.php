<?php declare(strict_types=1);

namespace tiFy\Metabox\Drivers\Slidefeed;

use tiFy\Contracts\Metabox\MetaboxDriver as MetaboxDriverContract;
use tiFy\Metabox\MetaboxDriver;
use tiFy\Support\Proxy\{Request, Router};

class Slidefeed extends MetaboxDriver
{
    /**
     * Indice de l'intance courante.
     * @var integer
     */
    static $instance = 0;

    /**
     * Url de traitement de requêtes XHR.
     * @var string
     */
    protected $url = '';

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        static::$instance++;
        $this->setUrl();
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return [
            'custom'  => true,
            'datas'   => ['image', 'title', 'url', 'caption'],
            'max'     => -1,
            'suggest' => true,
        ];
    }

    /**
     * @inheritDoc
     *
     * @return array {
     * @var string $name Nom de qualification d'enregistrement.
     * @var array $attrs Liste des attributs de balisae HTML du conteneur.
     * @var string $ajax_action Action Ajax de récupération des éléments.
     * @var array $editable Liste des interfaces d'édition des vignettes actives.
     * @var integer $max Nombre maximum de vignette.
     * @var array $args Liste des attribut de requête Ajax complémentaires.
     * @todo boolean|array $suggest Liste de selection de contenu.
     * @var boolean $custom Activation de l'ajout de vignettes personnalisées.
     * @var array $options Liste des options d'affichage.
     * @var array $viewer Liste des attributs de configuration du gestionnaire de gabarit.
     * @var string $item_class Traitement de l'affichage d'un élément
     * }
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'name'  => 'tify_taboox_slideshow',
            'title' => __('Diaporama', 'tify'),
        ]);
    }

    /**
     * Récupération de l'url de traitement XHR.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Définition de l'url de traitement XHR.
     *
     * @param string|null $url
     *
     * @return $this
     */
    public function setUrl(?string $url = null): self
    {
        $this->url = is_null($url)
            ? Router::xhr(md5('MetaboxSlidefeed--' . static::$instance), [$this, 'xhrResponse'])->getUrl()
            : $url;

        return $this;
    }

    /**
     * Définition d'un élément.
     *
     * @param int|string $index Indice de l'élément.
     * @param array $value Données.
     *
     * @return array
     */
    public function item($index, $value): array
    {
        $name = $this->get('name');
        $index = !is_numeric($index) ? $index : uniqid();

        return [
            'index' => $index,
            'name'  => $this->get('params.max', -1) === 1 ? "{$name}[]" : "{$name}[{$index}]",
            'value' => $value
        ];
    }

    /**
     * @inheritDoc
     */
    public function parse(): MetaboxDriverContract
    {
        parent::parse();

        $this->params([
            'attrs.data-options' => [

            ],
            'options'            => array_merge([
                'ratio'       => '16:9',
                'size'        => 'full',
                'nav'         => true,
                'tab'         => true,
                'progressbar' => false,
            ], $exists['options'] ?? []),
        ]);

        return $this;
    }

    /**
     * Récupération d'un élément via Ajax
     *
     * @param array ...$args Liste des arguments de requête passés dans l'url.
     *
     * @return array
     */
    public function xhrResponse(...$args): array
    {
        $index = Request::input('index');
        $max = Request::input('max', 0);
        $value = Request::input('value', '');

        if (($max > 0) && ($index >= $max)) {
            return [
                'success' => false,
                'data'    => __('Nombre maximum de vignette atteint.', 'tify'),
            ];
        } else {
            $this->set([
                'name'   => Request::input('name', []),
                'params' => [
                    'max' => $max,
                ],
                'viewer' => Request::input('viewer', []),
            ]);

            return [
                'success' => true,
                'data'    => (string)$this->viewer('item-wrap', $this->item($index, $value)),
            ];
        }
    }
}