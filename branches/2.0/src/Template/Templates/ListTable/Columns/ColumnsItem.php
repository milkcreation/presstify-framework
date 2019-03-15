<?php

namespace tiFy\Template\Templates\ListTable\Columns;

use Closure;
use tiFy\Kernel\Params\ParamsBag;
use tiFy\Support\HtmlAttrs;
use tiFy\Template\Templates\ListTable\Contracts\ColumnsItem as ColumnsItemContract;
use tiFy\Template\Templates\ListTable\Contracts\ListTable;

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
    protected $template;

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration personnalisés.
     * @param ListTable $template Instance du motif d'affichage associé.
     *
     * @return void
     */
    public function __construct($name, $attrs, ListTable $template)
    {
        $this->name = $name;
        $this->template = $template;

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
        if ($item = $this->template->item()) :
            if ($value = $item->get($this->getName())) :
                $type = (($db = $this->template->db()) && $db->existsCol($this->getName()))
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
            else :
                $content = $this->get('content');

                return $content instanceof Closure ? call_user_func_array($content, [$item]) : $content;
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
        return $this->template->viewer()->exists('tbody-col_' . $this->getName())
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
            $current_url = $this->template->request()->fullUrl();
            $current_url = remove_query_arg('paged', $current_url);
            $current_orderby = $this->template->request()->query('orderby');
            $current_order = $this->template->request()->query('order') === 'desc' ? 'desc' : 'asc';

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

        $template = $this->template->viewer()->exists('thead-col_' . $this->getName())
            ? 'thead-col_' . $this->getName() : 'thead-col';

        return $this->template->viewer(
            $template,
            [
                'attrs' => HtmlAttrs::createFromAttrs($attrs),
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
        return $this->template->columns()->getPrimary() === $this->getName();
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
        if ($item = $this->template->item()) :

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

            return $this->template->viewer(
                $this->getTemplate(),
                [
                    'item'    => $item,
                    'content' => $this->content(). ($this->isPrimary() ? $this->template->rowActions() : ''),
                    'attrs'   => HtmlAttrs::createFromAttrs($this->get('attrs', [])),
                ]
            );
        else :
            return '';
        endif;
    }
}