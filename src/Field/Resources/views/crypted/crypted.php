<?php
/**
 * @var tiFy\Field\FieldView $this
 */
?>
<?php $this->before(); ?>

<div <?php echo $this->htmlAttrs($this->get('container.attrs', [])); ?>>
    <div class="tiFyField-CryptedWrapper">
        <a href="#<?php echo $this->get('container.attrs.id'); ?>" class="tiFyField-CryptedToggle"
           aria-control="toggle"></a>

        <?php
        echo partial(
            'tag',
            [
                'tag'   => 'input',
                'attrs' => $this->get('attrs', []),
            ]
        );
        ?>
    </div>
</div>

<?php $this->after();