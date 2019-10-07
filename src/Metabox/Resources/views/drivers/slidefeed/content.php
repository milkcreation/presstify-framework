<?php
/**
 * @var tiFy\Contracts\Metabox\MetaboxView $this
 */
?>
<div <?php echo $this->htmlAttrs($this->params('attrs', [])); ?>>
    <?php if ($suggest = $this->params('suggest')) : ?>
        <?php echo field('suggest', $suggest); ?>
    <?php endif; ?>

    <?php if ($addnew = $this->params('addnew')) : ?>
        <?php echo field('button', $addnew); ?>
    <?php endif; ?>

    <ul data-control="metabox-slidefeed.items">
        <?php foreach ($this->get('items', []) as $item) : ?>
            <?php echo $this->insert('item-wrap', $item); ?>
        <?php endforeach; ?>
    </ul>

    <?php /*foreach ($this->get('options', []) as $k => $v) :
        echo field('hidden', [
            'name'  => "{$this->name()}[options][{$k}]",
            'value' => $v,
        ]);
    endforeach;*/ ?>
</div>
