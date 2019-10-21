<?php
/**
 * Champ de recherche.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\ListTable\Viewer $this
 */
?>
<p <?php echo $this->htmlAttrs($this->search()->get('attrs', [])); ?>>
    <?php echo field('label', [
        'attrs'   => [
            'class' => 'screen-reader-text',
            'for'   => $this->name(),
        ],
        'content' => $this->label('search_item'),
    ]); ?>
    <?php echo field('text', [
        'attrs' => [
            'id'   => $this->name(),
            'type' => 'search',
        ],
        'name'  => 's',
        'value' => $this->request()->input('s', ''),
    ]); ?>
    <?php echo field('button', [
        'attrs'   => [
            'id'    => 'search-submit',
            'class' => 'button',
            'type'  => 'submit',
        ],
        'content' => $this->label('search_item'),
    ]); ?>
</p>