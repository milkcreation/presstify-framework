<?php declare(strict_types=1);

namespace tiFy\Field\Fields\Repeater;

use tiFy\Contracts\Field\{FieldFactory as FieldFactoryContract, Repeater as RepeaterContract};
use tiFy\Field\FieldFactory;
use tiFy\Support\{Arr, Proxy\Request, Proxy\Router};

class Repeater extends FieldFactory implements RepeaterContract
{
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
        $this->setUrl();
    }

    /**
     * {@inheritDoc}
     *
     * @return array {
     * @var array $attrs Attributs HTML du champ.
     * @var string $after Contenu placé après le champ.
     * @var string $before Contenu placé avant le champ.
     * @var string $name Clé d'indice de la valeur de soumission du champ.
     * @var string $value Valeur courante de soumission du champ.
     * @var array $viewer Liste des attributs de configuration du pilote d'affichage.
     * @var array $ajax Liste des arguments de requête de récupération des éléments via Ajax.
     * @var array $args Arguments complémentaires porté par la requête Ajax.
     * @var array $button Liste des attributs de configuration du bouton d'ajout d'un élément.
     * @var int $max Nombre maximum de valeur pouvant être ajoutées. -1 par défaut, pas de limite.
     * @var boolean $removable Activation du déclencheur de suppression des éléments.
     * @var bool|array $sortable Activation de l'ordonnacemment des éléments|Liste des attributs de configuration.
     * @see http://api.jqueryui.com/sortable/
     * }
     */
    public function defaults(): array
    {
        return [
            'attrs'     => [],
            'after'     => '',
            'before'    => '',
            'name'      => '',
            'value'     => '',
            'viewer'    => [],
            'ajax'      => [],
            'args'      => [],
            'button'    => [],
            'classes'   => [],
            'max'       => -1,
            'removable' => true,
            'sortable'  => true,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @inheritDoc
     */
    public function parse(): FieldFactoryContract
    {
        parent::parse();

        $defaultClasses = [
            'addnew'  => 'FieldRepeater-addnew ThemeButton--primary ThemeButton--normal',
            'content' => 'FieldRepeater-itemContent',
            'down'    => 'FieldRepeater-itemSortDown ThemeFeed-itemSortDown',
            'item'    => 'FieldRepeater-item ThemeFeed-item',
            'items'   => 'FieldRepeater-items ThemeFeed-items',
            'order'   => 'FieldRepeater-itemOrder ThemeFeed-itemOrder',
            'remove'  => 'FieldRepeater-itemRemove ThemeFeed-itemRemove',
            'sort'    => 'FieldRepeater-itemSortHandle ThemeFeed-itemSortHandle',
            'up'      => 'FieldRepeater-itemSortUp ThemeFeed-itemSortUp',
        ];
        foreach ($defaultClasses as $k => $v) {
            $this->set(["classes.{$k}" => sprintf($this->get("classes.{$k}", '%s'), $v)]);
        }

        $this->set([
            'attrs.class'        => trim(sprintf($this->get('attrs.class', '%s'), ' FieldRepeater')),
            'attrs.data-id'      => $this->getId(),
            'attrs.data-control' => $this->get('attrs.data-control', 'repeater'),
        ]);

        $button = $this->get('button');
        $button = is_string($button) ? ['content' => $button] : $button;
        $button = array_merge([
            'tag'     => 'a',
            'content' => __('Ajouter un élément', 'tify'),
        ], $button);
        $this->set('button', $button);

        if (($this->get('button.tag') === 'a') && ! $this->get('button.attrs.href')) {
            $this->set('button.attrs.href', "#{$this->get('attrs.id')}");
        }
        $this->set('button.attrs.data-control', 'repeater.addnew');

        if ($sortable = $this->get('sortable')) {
            if ( ! is_array($sortable)) {
                $sortable = [];
            }
            $this->set('sortable', array_merge([
                'placeholder' => 'FieldRepeater-itemPlaceholder',
                'axis'        => 'y',
            ], $sortable));
        }

        $this->set('attrs.data-options', [
            'ajax'      => array_merge([
                'url'      => $this->getUrl(),
                'data'     => [
                    'viewer' => $this->get('viewer'),
                    'args'   => $this->get('args', []),
                    'max'    => $this->get('max'),
                    'name'   => $this->getName(),
                ],
                'dataType' => 'json',
                'method'   => 'post',
            ], $this->get('ajax', [])),
            'classes'   => $this->get('classes', []),
            'removable' => $this->get('removable'),
            'sortable'  => $this->get('sortable'),
        ]);

        $this->set('value', array_values(Arr::wrap($this->get('value', []))));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parseDefaults(): FieldFactoryContract
    {
        $this->parseViewer();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setUrl(?string $url = null): RepeaterContract
    {
        $this->url = is_null($url)
            ? Router::xhr(md5('FieldRepeater--' . $this->getIndex()), [$this, 'xhrResponse'])->getUrl()
            : $url;

        return $this;
    }

    /**
     * Génération de la réponse HTTP via une requête XHR.
     *
     * @return array
     */
    public function xhrResponse(): array
    {
        $max   = Request::input('max', -1);
        $index = Request::input('index', 0);

        $this->set([
            'name'   => Request::input('name', ''),
            'viewer' => Request::input('viewer', []),
        ])->parse();

        if (($max > 0) && ($index >= $max)) {
            return [
                'success' => false,
                'data'    => __('Nombre de valeur maximum atteinte', 'tify'),
            ];
        } else {
            return [
                'success' => true,
                'data'    => (string)$this->viewer('item-wrap', Request::all()),
            ];
        }
    }
}