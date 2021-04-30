<?php
/**
 * @var tiFy\Partial\PartialViewInterface $this
 */
?>
<?php $this->before(); ?>
<?php echo partial('tag', [
    'tag'     => 'div',
    'attrs'   => $this->get('attrs', []),
    'content' => $this->fetch('content', $this->all()) . $this->get('dismiss', '')
]); ?>
<?php $this->after();