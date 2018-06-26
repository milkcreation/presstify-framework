<?php

namespace tiFy\Components\Layout\ListTable\Column;

use ArrayIterator;
use Illuminate\Support\Arr;
use tiFy\Components\Layout\ListTable\ListTableInterface;
use tiFy\Apps\Attributes\AbstractAttributesIterator;
use tiFy\Partial\Partial;

class ColumnItemController extends AbstractAttributesIterator implements ColumnItemInterface
{
    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * Classe de rappel de la vue associée.
     * @var ListTableInterface
     */
    protected $app;

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
     * @param array|object $item Données de l'élément courant.
     * @param ListTableInterface $app Classe de rappel de la vue associée.
     *
     * @return void
     */
    public function __construct($name, $attrs = [], ListTableInterface $app)
    {
        $this->name = $name;

        parent::__construct($attrs, $app);
    }

    /**
     * {@inheritdoc}
     */
    public function display($item)
    {
        if (!$value = $item->get($this->name)) :
            return;
        endif;

        $type = (($db = $this->app->getDb()) && $db->existsCol($this->name)) ? strtoupper($db->getColAttr($this->name,
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

        $title = $this->getTitle();

        if ($this->isSortable()) :
            $current_url = $this->app->request()->currentUrl();
            $current_url = remove_query_arg('paged', $current_url);
            $current_orderby = $this->app->appRequest('GET')->get('orderby');
            $current_order = $this->app->appRequest('GET')->get('order') === 'desc' ? 'desc' : 'asc';

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

            $title = "<a href=\"" . esc_url(add_query_arg(compact('orderby', 'order'), $current_url)) . "\">" .
                "<span>{$title}</span><span class=\"sorting-indicator\"></span></a>";
        endif;

        $attrs = [
            'tag' => 'th',
            'attrs'  => [
                'class' => join(' ', $class),
                'scope' => 'col'
            ],
            'content' => $title
        ];

        if ($with_id) :
            $attrs['attrs']['id'] = $this->getName();
        endif;

        return (string)Partial::Tag($attrs);
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
        return $this->app->columns()->isPrimary($this->getName());
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