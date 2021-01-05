<?php
/**
 * @var tiFy\Metabox\MetaboxViewInterface $this
 */
?>
<h3 class="MetaboxSlidefeed-itemFieldLabel"><?php _e('Lien', 'tify'); ?></h3>
<?php echo field('text', [
    'name'  => $this->get('name') . '[url]',
    'value' => $this->get('value.url')
]);