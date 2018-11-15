<?php
/**
 * @var tiFy\Contracts\View\ViewController $this
 */
?>

<div class="MetaboxOptions-slideshow">
    <div class="MetaboxOptions-slideshowSelectors">
        <?php
        if ($this->get('suggest')) :
            echo field('select-js');
        endif;
        /* if ($this->args['suggest']) : ?>
            <div class="suggest tiFyTabooxSlideshowSelector-suggest">
                <?php
                $suggest_args = (is_array($this->args['suggest'])) ? $this->args['suggest'] : [];
                $suggest_args = wp_parse_args(
                    $suggest_args,
                    [
                        'class'       => 'tify_taboox_slideshow-suggest',
                        'placeholder' => __('Rechercher parmi les contenus du site', 'tify'),
                        'elements'    => ['ico', 'title', 'type', 'status', 'id'],
                        'query_args'  => ['post_type' => 'any', 'posts_per_page' => -1],
                        'attrs'       => ['data-duplicate' => $this->args['duplicate']]
                    ]
                );
                Control::Suggest($suggest_args, true);
                ?>
            </div>
        <?php endif; */
        ?>

        <?php if ($this->get('suggest') && $this->get('custom')) : ?>
            <p><?php _e('ou', 'tify'); ?></p>
        <?php endif; ?>

        <?php
        if ($this->get('custom')) :
            echo field(
                'button',
                [
                    'before'  => '<div>',
                    'after'   => '</div>',
                    'attrs'   => [
                        'class' => 'button-secondary'
                    ],
                    'content' => __('Vignette personnalisée', 'tify')
                ]
            );
        endif;
        ?>
    </div>

    <div class="items tiFyTabooxSlideshowList">
        <div class="tiFyTabooxSlideshowList-overlay">
            <?php _e('Chargement ...', 'tify'); ?>
        </div>

        <?php
        foreach ($this->get('options', []) as $k => $v) :
            echo field(
                'hidden',
                [
                    'name'  => "{$this->get('name')}[options][{$k}]",
                    'value' => $v
                ]
            );
        endforeach;
        ?>

        <ul class="MetaboxOptions-slideshowItems">
            <?php
            foreach ($this->get('items', []) as $index => $attrs) :
                $this->insert('item', $attrs);
            endforeach;
            ?>
        </ul>
    </div>
</div>
