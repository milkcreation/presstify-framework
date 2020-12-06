<?php
/**
 * @var tiFy\Partial\PartialView $this
 * @var tiFy\Wordpress\Contracts\Query\QueryPost $post
 */
?>
<div class="FieldSuggest-pickerItemWrapper FieldSuggest-pickerItemWrapper--post">
    <div class="FieldSuggest-pickerItemThumbnail">
        <?php if ($thumbnail = $post->getThumbnail([50, 50])) : ?>
            <?php echo $post->getThumbnail([50, 50]); ?>
        <?php else : ?>
            <?php echo partial('holder', [
                'width'  => 50,
                'height' => 50,
                'content' => __('indispo.', 'tify')
            ]); ?>
        <?php endif; ?>
    </div>

    <div class="FieldSuggest-pickerItemTitle">
        <?php echo $post->getTitle(); ?>
    </div>
</div>