<?php
/**
 * @var tiFy\Contracts\View\ViewController $this
 */
?>

<li class="MetaboxOptions-slideshowItem">
    <div class="MetaboxOptions-slideshowItemInputs">
        <div class="col col-left">
            <?php $this->insert('item-image', $this->all()); ?>
        </div>

        <div class="col col-right">
            <?php
            echo field(
                'hidden',
                [
                    'name'      => "{$this->get('name')}[post_id]",
                    'value'     => $this->get('post_id')
                ]
            );
            ?>

            <?php $this->insert('item-title', $this->all()); ?>

            <?php $this->insert('item-url', $this->all()); ?>

            <?php $this->insert('item-caption', $this->all()); ?>

        </div>
    </div>

    <?php $this->insert('item-helpers', $this->all()); ?>
</li>