<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

use tiFy\Support\ParamsBag;
use tiFy\Template\Factory\FactoryAwareTrait;
use tiFy\Template\Templates\ListTable\Contracts\{ListTable, RowAction as RowActionContract};

class RowAction extends ParamsBag implements RowActionContract
{
    use FactoryAwareTrait;

    /**
     * Instance du gabarit associé.
     * @var ListTable
     */
    protected $factory;

    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return (string) $this->render();
    }

    /**
     * Liste des attributs de configuration.
     * @return array {
     *      @var string $content Contenu du lien (chaîne de caractère ou éléments HTML).
     *      @var array $attrs Liste des attributs complémentaires de la balise du lien.
     *      @var array $query_args Tableau associatif des arguments passés en requête dans l'url du lien.
     *      @var bool|string $nonce Activation de la création de l'identifiant de qualification de la clef de
     *                              sécurisation passée en requête dans l'url du lien|Identifiant de qualification
     *                              de la clef de sécurisation.
     *      @var bool|string $referer Activation de l'argument de l'url de référence passée en requête dans l'url du
     *                                lien.
     * }
     */
    public function defaults()
    {
        return [
            'content'    => '',
            'attrs'      => [],
            'query_args' => [],
            'nonce'      => true,
            'referer'    => true
        ];
    }

    /**
     * @inheritDoc
     */
    public function getNonce(): string
    {
        /*if (($item_index_name = $this->factory->param('item_index_name')) && isset($this->item->{$item_index_name})) {
            $item_index = $this->item->{$item_index_name};
        } else { */
            $item_index = '';
        //};

        if(!$item_index) {
        } elseif(!is_array($item_index)) {
            $item_index = array_map('trim', explode(',', $item_index));
        }

        $nonce_action = (!$item_index || (count($item_index) === 1))
            ? $this->factory->param('singular') . '-' . $this->name
            : $this->factory->param('plural') . '-' . $this->name;

        if ($item_index && count($item_index) === 1) {
            $nonce_action .= '-' . reset($item_index);
        }

        return sanitize_title($nonce_action);
    }

    /**
     * @inheritDoc
     */
    public function isActive(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function parse()
    {
        parent::parse();

        if (!$this->get('attrs.href')) {
            $this->set('attrs.href', $this->factory->param('page_url'));
        }

        if($query_args = $this->get('query_args', [])) {
            $this->set('attrs.href', add_query_arg($query_args, $this->get('attrs.href')));
        }

        if ($nonce = $this->get('nonce')) {
            if ($nonce === true) {
                $nonce = $this->factory->param('page_url');
            }
            $this->set('attrs.href', wp_nonce_url($this->get('attrs.href'), $nonce));
        }

        if ($referer = $this->get('referer')) {
            if ($referer === true) {
                $referer = set_url_scheme('//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            }
            $this->set('attrs.href', add_query_arg([
                '_wp_http_referer' => urlencode(
                    wp_unslash($referer)
                )
            ], $this->get('attrs.href')));
        }

        // Argument de requête par défaut
        /*$default_query_args = [
            'action' => $row_action_name
        ];
        if (($item_index_name = $this->getParam('item_index_name')) && isset($item->{$item_index_name})) {
            $default_query_args[$item_index_name] = $item->{$item_index_name};
        }
        $href = \add_query_arg(
            $default_query_args,
            $href
        );*/

        if (!$this->get('content')) {
            $this->set('content', $this->name);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        if ($this->get('hide_empty') && !$this->get('count_items', 0)) {
            return '';
        }

        return (string)partial('tag', [
            'tag'       => 'a',
            'attrs'     => $this->get('attrs', []),
            'content'   => $this->get('content')
        ]);
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): RowActionContract
    {
        $this->name = $name;

        return $this;
    }
}