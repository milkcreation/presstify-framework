<?php
/**
 * Champ de recherche.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\ListTable\Viewer\Viewer $this
 */
?>
<?php if ($this->items()->exists() || !$this->request()->searchExists()) : ?>
    <p class="search-box">
        <?php
        echo field(
            'label',
            [
                'attrs'   => [
                    'class' => 'screen-reader-text',
                    'for'   => $this->name(),
                ],
                'content' => $this->label('search_items'),
            ]
        );
        ?>

        <?php
        echo field(
            'text',
            [
                'attrs' => [
                    'id'   => $this->name(),
                    'type' => 'search',
                ],
                'name'  => 's',
                'value' =>$this->request()->searchTerm(),
            ]
        );
        ?>

        <?php
        echo field(
            'button',
            [
                'attrs'   => [
                    'id'    => 'search-submit',
                    'class' => 'button',
                    'type'  => 'submit',
                ],
                'content' => $this->label('search_items'),
            ]
        )
        ?>
    </p>
<?php endif; ?>