<?php
/**
 * @var tiFy\Contracts\Metabox\MetaboxView $this
 * @var tiFy\Wordpress\Contracts\QueryPost $item
 */
?>
<?php if ($thumbnail = $item->getThumbnail('thumbnail', ['class' => 'MetaboxRelatedPost-itemThumbImg'])) : ?>
    <figure class="MetaboxRelatedPost-itemThumb"><?php echo $thumbnail; ?></figure>
<?php else : ?>
    <figure class="MetaboxRelatedPost-itemThumb">
        <?php echo partial('holder', ['content' => __('indispo.', 'tify')]); ?>
    </figure>
<?php endif; ?>

<h4 class="MetaboxRelatedPost-itemTitle"><?php echo $item->getTitle(); ?></h4>

<ul class="MetaboxRelatedPost-itemMetas" data-control="metabox.related-post.item.metas">
    <li class="MetaboxRelatedPost-itemMeta MetaboxRelatedPost-itemMeta--post_type">
        <label><?php _e('Type :', 'tify'); ?></label>
        <?php echo ucfirst($item->getType()->label('singular_name')); ?>
    </li>
    <li class="MetaboxRelatedPost-itemMeta MetaboxRelatedPost-itemMeta--post_status">
        <label><?php _e('Statut :', 'tify'); ?></label>
        <?php echo $item->getStatus()->getLabel(); ?>
    </li>
</ul>