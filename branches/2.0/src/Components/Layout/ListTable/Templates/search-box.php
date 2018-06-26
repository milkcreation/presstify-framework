<?php
/**
 * Champ de recherche.
 *
 * @var tiFy\Components\Layout\ListTable\TemplateController $this
 */
?>

<?php if ($this->hasItems() || !empty($_REQUEST['s'])) : ?>
<p class="search-box">
    <?php
        echo tify_field_label(
            [
                'attrs' => [
                    'class' => 'screen-reader-text',
                    'for'   => $this->getName()
                ],
                'content' => $this->getLabel('search_items')
            ]
        );
    ?>

    <?php
        echo tify_field_text(
            [
                'attrs' => [
                    'id'   => $this->getName(),
                    'type' => 'search',
                ],
                'name'  => 's',
                'value' => isset($_REQUEST['s']) ? esc_attr(wp_unslash($_REQUEST['s'])) : ''
            ]
        );
    ?>

    <?php
        echo tify_field_button(
            [
                'attrs' => [
                    'id'    => 'search-submit',
                    'class' => 'button',
                    'type'  => 'submit'
                ],
                'content' => $this->getLabel('search_items')
            ]
        )
    ?>
</p>
<?php endif; ?>