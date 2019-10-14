<?php
/**
 * @var tiFy\Partial\PartialView $this
 */
?>
<?php $this->before(); ?>
    <div <?php $this->attrs(); ?>>
        <?php echo $this->get('backdrop_close', ''); ?>

        <div data-control="modal.dialog" class="<?php echo $this->get('size'); ?>">
            <div data-control="modal.content">
                <?php if (is_string($this->get('content'))) : ?>
                    <?php echo $this->get('content'); ?>
                <?php else : ?>
                    <?php if ($header = $this->get('content.header', '')): ?>
                        <div data-control="modal.content.header"><?php echo $header; ?></div>
                    <?php endif; ?>
                    <?php if ($body = $this->get('content.body', '')): ?>
                        <div data-control="modal.content.body"><?php echo $body; ?></div>
                    <?php endif; ?>
                    <?php if ($footer = $this->get('content.footer', '')): ?>
                        <div data-control="modal.content.footer"><?php echo $footer; ?></div>
                    <?php endif; ?>
                    <?php if ($close = $this->get('content.close', '')): ?>
                        <button type="button" data-dismiss="modal" data-control="modal.close"><?php echo $close; ?></button>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php $this->after();