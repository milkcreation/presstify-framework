<?php
/**
 * @var tiFy\Contracts\Metabox\MetaboxView $this
 */
?>
<div>
    <?php foreach ($this->get('datas', []) as $edit) : ?>
        <?php //$this->insert("data-{$edit}", $this->all()); ?>
    <?php endforeach; ?>
</div>