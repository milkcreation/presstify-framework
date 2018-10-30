<?php
/**
 * @var \tiFy\Contracts\Views\ViewInterface $this
 */
?>

<div class="MetaboxOptions-slideshowItemInput MetaboxOptions-slideshowItemInput--image">
    <?php
    echo field(
        'media-image',
        [
            'name'      => "{$this->get('name')}[attachment_id]",
            'value'     => $this->get('attachment_id'),
            'default'   => get_post_thumbnail_id($this->get('post_id', 0)),
            'size'      => 'thumbnail',
            'size_info' => false,
            'width'     => 150,
            'height'    => 150
        ]
    );
    ?>
</div>
