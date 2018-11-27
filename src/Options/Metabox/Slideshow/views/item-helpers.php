<?php
/**
 * @var tiFy\Contracts\View\ViewController $this
 */
?>

<div class="MetaboxOptions-slideshowItemHelpers">
    <div class="MetaboxOptions-slideshowItemHelper MetaboxOptions-slideshowItemHelper--order">
        <?php
        echo field(
            'text',
            [
                'name'  => "{$this->get('name')}[order]",
                'value' => $this->get('order'),
                'attrs' => [
                    'readonly'
                ]
            ]
        );
        ?>
    </div>

    <a href="#sort" class="MetaboxOptions-slideshowItemHelper MetaboxOptions-slideshowItemHelper--sort"></a>

    <a href="#order" class="MetaboxOptions-slideshowItemHelper MetaboxOptions-slideshowItemHelper--remove"></a>

    <span class="MetaboxOptions-slideshowItemHelper MetaboxOptions-slideshowItemHelper--infos">
        <?php echo $this->get('post_id') ? __('Contenu du site', 'tify') : __('Vignette personnalisée', 'tify'); ?>
    </span>
</div>