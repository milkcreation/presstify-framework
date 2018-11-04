<?php

namespace tiFy\Layout\Share\ListTable\Column;

use ArrayIterator;
use Illuminate\Support\Arr;
use tiFy\Kernel\Params\ParamsBag;
use tiFy\Kernel\Tools;
use tiFy\Layout\Share\ListTable\Contracts\ColumnItemInterface;
use tiFy\Layout\Share\ListTable\Contracts\ListTableInterface;

class ColumnItemController extends ParamsBag implements ColumnItemInterface
{
    /**
     * Instance de la disposition associée.
     * @var ListTableInterface
     */
    protected $layout;

    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * Liste des attributs de configuration.
     * @return array
     */
    protected $attributes = [
        'content'  => '',
        'name'     => '',
        'title'    => '',
        'sortable' => false,
        'hidden'   => false,
        'primary'  => false
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration personnalisés.
     * @param ListTableInterface $layout Instance de la disposition associée..
     *
     * @return void
     */
    public function __construct($name, $attrs = [], ListTableInterface $layout)
    {
        $this->name = $name;
        $this->layout = $layout;

        parent::__construct($attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'title' => $this->getName()
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function display($item)
    {
        if ($value = $item->get($this->name)) :
            $type = (($db = $this->layout->db()) && $db->existsCol($this->name)) ? strtoupper($db->getColAttr($this->name,
                'type')) : '';

            switch ($type) :
                default:
                    if (is_array($value)) :
                        return join(', ', $value);
                    else :
                        return $value;
                    endif;
                    break;
                case 'DATETIME' :
                    return \mysql2date(get_option('date_format') . ' @ ' . get_option('time_format'), $value);
                    break;
            endswitch;
        elseif (Tools::Functions()->isCallable($this->get('content'))) :
            return call_user_func($this->get('content'), $item);
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
    public function getTitle()
    {
        return $this->get('title');
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader($with_id = true)
    {
        $class = ['manage-column', "column-{$this->getName()}"];

        if ($this->isHidden()) :
            $class[] = 'hidden';
        endif;

        if ($this->isPrimary()) :
            $class[] = 'column-primary';
        endif;

        $attrs = [
            'tag' => 'th',
            'attrs'  => [
                'class' => join(' ', $class),
                'scope' => 'col'
            ],
            'content' => $this->getHeaderContent()
        ];

        if ($with_id) :
            $attrs['attrs']['id'] = $this->getName();
        endif;

        return (string)partial('tag', $attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderContent()
    {
        $content = $this->getTitle();

        if ($this->isSortable()) :
            $current_url = $this->layout->request()->fullUrl();
            $current_url = remove_query_arg('paged', $current_url);
            $current_orderby = $this->layout->request()->query('orderby');
            $current_order = $this->layout->request()->query('order') === 'desc' ? 'desc' : 'asc';

            list($orderby, $desc_first) = $this->get('sortable');

            if ( $current_orderby === $orderby ) :
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
                    'tag' => 'a',
                    'attrs' => [
                        'href' => esc_url(add_query_arg(compact('orderby', 'order'), $current_url))
                    ],
                    'content' => "<span>{$content}</span><span class=\"sorting-indicator\"></span></a>"
                ]
            );
        endif;

        return $content;
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
        return $this->layout->columns()->isPrimary($this->getName());
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

        if ($sortable = $this->get('sortable')) :
            $this->set(
                'sortable',
                is_bool($sortable)
                    ? [$this->getName(), false]
                    : (is_string($sortable) ? [$sortable, false] : $sortable)
            );
        endif;

        $this->set('name', $this->getName());
    }
}