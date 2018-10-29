<?php
/**
 * @var \tiFy\Contracts\Views\ViewInterface $this
 */
?>

<div class="MetaboxOptions-slideshowItemInput MetaboxOptions-slideshowItemInput--title">
    <h3><?php _e('Lien', 'tify'); ?></h3>

    <label>
        <?php
        echo field(
            'checkbox',
            [
                'name'      => "{$this->get('name')}[clickable]",
                'value'     => 1,
                'checked'   => $this->get('clickable', 0),
                'attrs'     => [
                    'autocomplete' => 'off'
                ],
                'after'     => __('Vignette cliquable', 'tify')
            ]
        );
        ?>
    </label>

    <?php
    $attrs = $this->get('post_id') ? ['readonly'] : [];
    $attrs['placeholder'] = __('Saisissez l\'url au clic.', 'tify');

    echo field(
        'text',
        [
            'name'      => "{$this->get('name')}[url]",
            'value'     => $this->get('url'),
            'attrs'     => $attrs
        ]
    );
    ?>
</div>