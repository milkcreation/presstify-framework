<?php

namespace tiFy\View\Pattern\ListTable\Columns;

use tiFy\Kernel\Params\ParamsBag;
use tiFy\Kernel\Tools;
use tiFy\View\Pattern\ListTable\Contracts\ColumnsItem as ColumnsItemContract;
use tiFy\View\Pattern\ListTable\Contracts\ListTable;

class ColumnsItem extends ParamsBag implements ColumnsItemContract
{
    /**
     * Liste des attributs de configuration.
     * @return array
     */
    protected $attributes = [
        'content'  => '',
        'title'    => '',
        'sortable' => false,
        'hidden'   => false,
        'primary'  => false,
        'attrs'    => [],
    ];

    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * Instance du motif d'affichage associé.
     * @var ListTable
     */
    protected $pattern;

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration personnalisés.
     * @param ListTable $pattern Instance du motif d'affichage associé.
     *
     * @return void
     */
    public function __construct($name, $attrs, ListTable $pattern)
    {
        $this->name = $name;
        $this->pattern = $pattern;

        parent::__construct($attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string)$this->render();
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'title' => $this->getName(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function content()
    {
        if ($item = $this->pattern->item()) :
            if ($value = $item->get($this->getName())) :
                $type = (($db = $this->pattern->db()) && $db->existsCol($this->getName()))
                    ? strtoupper($db->getColAttr($this->getName(), 'type'))
                    : '';

                switch ($type) :
                    default:
                        if (is_array($value)) :
                            return join(', ', $value);
                        else :
                            return $value;
                        endif;
                        break;
                    case 'DATETIME' :
                        return mysql2date(get_option('date_format') . ' @ ' . get_option('time_format'), $value);
                        break;
                endswitch;
            elseif (Tools::Functions()->isCallable($this->get('content'))) :
                return call_user_func($this->get('content'), $item);
            else :
                return $this->get('content');
            endif;
        else :
            return $this->get('content');
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate($default = 'tbody-col')
    {
        return $this->pattern->viewer()->exists('tbody-col_' . $this->getName())
            ? 'tbody-col_' . $this->getName() : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->get('title');
    }

    /**
     * {@inheritdoc}
     */
    public function header($with_id = true)
    {
        $classes = ['manage-column', "column-{$this->getName()}"];

        if ($this->isHidden()) :
            $classes[] = 'hidden';
        endif;

        if ($this->isPrimary()) :
            $classes[] = 'column-primary';
        endif;

        $attrs = [];
        if ($with_id) :
            $attrs['id'] = $this->getName();
        endif;

        $attrs['class'] = join(' ', $classes);

        $attrs['scope'] = 'col';

        $content = $this->getTitle();

        if ($this->isSortable()) :
            $current_url = $this->pattern->request()->fullUrl();
            $current_url = remove_query_arg('paged', $current_url);
            $current_orderby = $this->pattern->request()->query('orderby');
            $current_order = $this->pattern->request()->query('order') === 'desc' ? 'desc' : 'asc';

            list($orderby, $desc_first) = $this->get('sortable');

            if ($current_orderby === $orderby) :
                $order = 'asc' === $current_order ? 'desc' : 'asc';
                $class[] = 'sorted';
                $class[] = $current_order;
            else :
                $order = $desc_first ? 'desc' : 'asc';
                $class[] = 'sortable';
                $class[] = $desc_first ? 'asc' : 'desc';
            endif;

            $content = (string)partial(
                'tag',
                [
                    'tag'     => 'a',
                    'attrs'   => [
                        'href' => esc_url(add_query_arg(compact('orderby', 'order'), $current_url)),
                    ],
                    'content' => "<span>{$content}</span><span class=\"sorting-indicator\"></span></a>",
                ]
            );
        endif;

        $template = $this->pattern->viewer()->exists('thead-col_' . $this->getName())
            ? 'thead-col_' . $this->getName() : 'thead-col';

        return $this->pattern->viewer(
            $template,
            [
                'attrs' => Tools::Html()->parseAttrs($attrs),
                'content' => $content
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return !empty($this->get('hidden'));
    }

    /**
     * {@inheritdoc}
     */
    public function isPrimary()
    {
        return $this->pattern->columns()->getPrimary() === $this->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function isSortable()
    {
        return !empty($this->get('sortable'));
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->set('name', $this->getName());

        if ($sortable = $this->get('sortable')) :
            $this->set(
                'sortable',
                is_bool($sortable)
                    ? [$this->getName(), false]
                    : (is_string($sortable) ? [$sortable, false] : $sortable)
            );
        endif;

        $this->set(
            'attrs.class', trim($this->get('attrs.class') . "{$this->getName()} column-{$this->getName()}")
        );

        $this->set('attrs.data-colname', wp_strip_all_tags($this->getTitle()));
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        if ($item = $this->pattern->item()) :

            $classes = '';
            if ($this->isPrimary()) :
                $classes .= 'has-row-actions column-primary';
            endif;
            if ($this->isHidden()) :
                $classes .= 'hidden';
            endif;
            if ($classes) :
                $this->set('attrs.class', trim($this->get('attrs.class', '') . " {$classes}"));
            endif;

            return $this->pattern->viewer(
                $this->getTemplate(),
                [
                    'item'    => $item,
                    'content' => $this->content(). ($this->isPrimary() ? $this->pattern->rowActions() : ''),
                    'attrs'   => Tools::Html()->parseAttrs($this->get('attrs', [])),
                ]
            );
        else :
            return '';
        endif;
    }
}