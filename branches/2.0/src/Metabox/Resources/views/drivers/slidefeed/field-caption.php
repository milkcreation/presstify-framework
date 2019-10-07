<?php
/**
 * @var tiFy\Contracts\Metabox\MetaboxView $this
 */
?>
<h3 class="MetaboxSlidefeed-itemFieldLabel"><?php _e('Légende', 'tify'); ?></h3>
<?php echo field('tinymce', [
    'name'  => $this->get('name') . '[caption]',
    'value' => $this->get('value.caption'),
]);