<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

use Closure;
use tiFy\Support\HtmlAttrs;
use tiFy\Support\ParamsBag;
use tiFy\Template\Factory\FactoryAwareTrait;
use tiFy\Template\Templates\ListTable\Contracts\{Column as ColumnContract, ListTable};

class Column extends ParamsBag implements ColumnContract
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
     * @inheritdoc
     */
    public function __toString(): string
    {
        return (string)$this->render();
    }

    /**
     * @inheritdoc
     */
    public function defaults(): array
    {
        return [
            'attrs'    => [],
            'content'  => '',
            'hideable' => true,
            'hidden'   => false,
            'primary'  => false,
            'sortable' => false,
            'title'    => $this->getName()
        ];
    }

    /**
     * @inheritdoc
     */
    public function content(): string
    {
        if ($item = $this->factory->item()) {
            if ($value = $item->get($this->getName())) {
                $type = '';
                /**
                 * @todo

                (
                    ($db = $this->factory->db()) &&
                    $db->getConnection()->getSchemaBuilder()->hasColumn($db->getTable(), $this->getName()))
                    ? strtoupper($db->getColAttr($this->getName(), 'type'))
                    : '';
                 */
                switch ($type) {
                    default:
                        return is_array($value) ? join(', ', $value) : $value;
                        break;
                    case 'DATETIME' :
                        return mysql2date(get_option('date_format') . ' @ ' . get_option('time_format'), $value);
                        break;
                }
            } else {
                $content = $this->get('content');

                return $content instanceof Closure ? call_user_func_array($content, [$item]) : $content;
            }
        } else {
            return $this->get('content');
        }
    }

    /**
     * @inheritdoc
     */
    public function cellAttrs(): string
    {
        return HtmlAttrs::createFromAttrs($this->get('attrs', []));
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getTemplate(string $default = 'tbody-col'): string
    {
        return $this->factory->viewer()->exists('_override::tbody-col_' . $this->getName())
            ? 'tbody-col_' . $this->getName() : $default;
    }

    /**
     * @inheritdoc
     */
    public function getTitle(): string
    {
        return $this->get('title');
    }

    /**
     * @inheritdoc
     */
    public function header(bool $with_id = true): string
    {
        $classes = ['manage-column', "column-{$this->getName()}"];

        if ($this->isHidden()) {
            $classes[] = 'hidden';
        }

        if ($this->isPrimary()) {
            $classes[] = 'column-primary';
        }

        $attrs = [];
        if ($with_id) {
            $attrs['id'] = $this->getName();
        }

        $attrs['class'] = join(' ', $classes);

        $attrs['scope'] = 'col';

        $content = $this->getTitle();

        if ($this->isSortable()) {
            $current_url = $this->factory->request()->fullUrl();
            $current_url = remove_query_arg('paged', $current_url);
            $current_orderby = $this->factory->request()->input('orderby');
            $current_order = ($this->factory->request()->input('order') === 'desc') ? 'desc' : 'asc';

            list($orderby, $desc_first) = $this->get('sortable');

            if ($current_orderby === $orderby) {
                $order = 'asc' === $current_order ? 'desc' : 'asc';
                $class[] = 'sorted';
                $class[] = $current_order;
            } else {
                $order = $desc_first ? 'desc' : 'asc';
                $class[] = 'sortable';
                $class[] = $desc_first ? 'asc' : 'desc';
            }

            $content = (string)partial('tag', [
                'tag'     => 'a',
                'attrs'   => [
                    'href' => esc_url(add_query_arg(compact('orderby', 'order'), $current_url)),
                ],
                'content' => "<span>{$content}</span><span class=\"sorting-indicator\"></span></a>",
            ]);
        }

        $name = $this->factory->viewer()->exists('thead-col_' . $this->getName())
            ? 'thead-col_' . $this->getName() : 'thead-col';

        return (string)$this->factory->viewer($name, [
            'attrs'   => HtmlAttrs::createFromAttrs($attrs),
            'content' => $content
        ]);
    }

    /**
     * @inheritdoc
     */
    public function isHidden(): bool
    {
        return !empty($this->get('hidden'));
    }

    /**
     * @inheritdoc
     */
    public function isPrimary(): bool
    {
        return $this->factory->columns()->getPrimary() === $this->getName();
    }

    /**
     * @inheritdoc
     */
    public function isSortable(): bool
    {
        return !empty($this->get('sortable'));
    }

    /**
     * @inheritdoc
     */
    public function isVisible(): bool
    {
        return !$this->isHidden();
    }

    /**
     * @inheritdoc
     */
    public function parse(): ColumnContract
    {
        parent::parse();

        $this->set('name', $this->getName());

        if ($sortable = $this->get('sortable')) {
            $this->set(
                'sortable',
                is_bool($sortable)
                    ? [$this->getName(), false]
                    : (is_string($sortable) ? [$sortable, false] : $sortable)
            );
        }

        $this->set('attrs.class', trim($this->get('attrs.class') . "{$this->getName()} column-{$this->getName()}"));

        $this->set('attrs.data-colname', wp_strip_all_tags($this->getTitle()));

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function render(): string
    {
        if ($item = $this->factory->item()) {
            $classes = '';
            if ($this->isPrimary()) {
                $classes .= 'has-row-actions column-primary';
            }

            if ($this->isHidden()) {
                $classes .= 'hidden';
            }

            if ($classes) {
                $this->set('attrs.class', trim($this->get('attrs.class', '') . " {$classes}"));
            }

            return (string)$this->factory->viewer($this->getTemplate(), [
                'column'  => $this,
                'content' => $this->content() . ($this->isPrimary() ? $this->factory->rowActions() : ''),
                'item'    => $item
            ]);
        } else {
            return '';
        }
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): ColumnContract
    {
        $this->name = $name;

        return $this;
    }
}