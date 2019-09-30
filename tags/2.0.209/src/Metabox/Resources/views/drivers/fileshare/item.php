<?php
/**
 * @var tiFy\Contracts\Metabox\MetaboxView $this
 */
?>
<span class="MetaboxFileshare-itemIcon">
    <?php echo $this->get('icon'); ?>
</span>

<span class="MetaboxFileshare-itemTitle">
    <?php echo $this->get('title'); ?>
</span>

<span class="MetaboxFileshare-itemMime">
    <?php echo $this->get('mime'); ?>
</span>

<?php echo field('hidden', [
    'name'  => $this->get('name'),
    'value' => $this->get('value')
]);