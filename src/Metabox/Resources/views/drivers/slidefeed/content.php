<?php
/**
 * @var tiFy\Contracts\Metabox\MetaboxView $this
 */
?>
<div <?php echo $this->htmlAttrs($this->params('attrs', [])); ?>>
    <div>
        <?php
        /**
         * @todo
         *
         * if ($this->get('suggest')) :
         * echo field(
         * 'select-js',
         * [
         * 'max'          => 1,
         * 'removable'    => false,
         * 'sortable'     => false,
         * 'autocomplete' => true,
         * 'source'       => [
         * 'query_args' => [
         * 'post_type'      => 'post',
         * 'posts_per_page' => 2
         * ]
         * ]
         * ]
         * );
         * endif;
         * ?>
         *
         * <?php if ($this->get('suggest') && $this->get('custom')) : ?>
         * <p><?php _e('ou', 'tify'); ?></p>
         * <?php endif; */ ?>

        <?php if ($this->params('custom')) : ?>
            <?php echo field('button', [
                'content' => __('Vignette personnalisée', 'tify'),
            ]); ?>
        <?php endif; ?>
    </div>

    <div>
        <div class="MetaboxOptions-slideshowListOverlay">
            <?php _e('Chargement ...', 'tify'); ?>
        </div>

        <?php foreach ($this->get('options', []) as $k => $v) :
            echo field('hidden', [
                'name'  => "{$this->name()}[options][{$k}]",
                'value' => $v,
            ]);
        endforeach; ?>

        <ul data-control="metabox-slidefeed.items">
            <?php foreach ($this->get('items', []) as $item) : ?>
                <?php echo $this->insert('item-wrap', $item); ?>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
