<?php
/**
 * @var tiFy\Partial\PartialView $this
 */
?>
<?php $this->before(); ?>
<?php
echo partial('notice', [
    'attrs'   => $this->get('attrs', []),
    'content' => $this->get('content', '') . $this->get('accept', ''),
    'dismiss' => $this->get('dismiss', '')
]);
?>
<?php $this->after();