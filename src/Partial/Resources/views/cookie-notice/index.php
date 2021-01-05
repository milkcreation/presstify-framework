<?php
/**
 * @var tiFy\Partial\PartialViewInterface $this
 */
?>
<?php $this->before(); ?>
<?php echo partial('notice', [
    'attrs'   => $this->get('attrs', []),
    'content' => $this->get('content', ''),
    'dismiss' => $this->get('dismiss', '')
]); ?>
<?php $this->after();