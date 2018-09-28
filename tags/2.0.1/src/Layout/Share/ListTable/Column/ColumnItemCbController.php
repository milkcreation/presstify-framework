<?php

namespace tiFy\Layout\Share\ListTable\Column;

class ColumnItemCbController extends ColumnItemController
{
    static $counter = 1;

    /**
     * {@inheritdoc}
     */
    public function display($item)
    {
        return (($db = $this->layout->db()) && ($primary = $db->getPrimary()) && isset($item->{$primary}))
            ? sprintf('<input type="checkbox" name="%1$s[]" value="%2$s" />', $primary, $item->{$primary})
            : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader($with_id = true)
    {
        $class = ['manage-column', "column-{$this->getName()}", 'check-column'];

        $attrs = [
            'tag' => 'td',
            'attrs'  => [
                'class' => join(' ', $class),
                'scope' => 'col'
            ],
            'content' => $this->getHeaderContent()
        ];

        self::$counter++;

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
        $content = (string)field(
            'label',
            [
                'attrs' => [
                    'class' => 'screen-reader-text',
                    'for'   => 'cb-select-all-' . self::$counter
                ],
                'content' => __( 'Select All' )
            ]
        );
        $content .= (string)field(
            'checkbox',
            [
                'attrs' => [
                    'id' => 'cb-select-all-' . self::$counter
                ]
            ]
        );

        return $content;
    }
}