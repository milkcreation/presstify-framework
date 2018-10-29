<?php
/**
 * @var \tiFy\Contracts\Views\ViewInterface $this
 */
?>

<div class="MetaboxOptions-slideshowItemInput MetaboxOptions-slideshowItemInput--caption">
    <h3><?php _e('Légende', 'tify'); ?></h3>

    <div id="<?php echo "{$this->get('name')}[caption]"; ?>" class="tinymce-editor">
        <?php echo $this->get('caption'); ?>
    </div>
</div>