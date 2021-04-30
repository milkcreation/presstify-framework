<?php
/**
 * @var tiFy\Partial\PartialViewInterface $this
 * @var array $types
 */
?>
<?php foreach($types as $type) : ?>
    <?php foreach ($this->get($type, []) as $notice) : ?>
        <?php echo partial('notice', array_merge($notice['attrs'] ? : [], [
            'type'      => $notice['type'] ?? 'error',
            'content'   => $notice['message']
        ])); ?>
    <?php endforeach; ?>
<?php endforeach;