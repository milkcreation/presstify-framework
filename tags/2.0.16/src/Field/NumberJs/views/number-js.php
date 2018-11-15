<?php
/**
 * @var tiFy\Field\FieldView $this
 */
?>

<?php $this->before(); ?>
    <div <?php echo $this->getHtmlAttrs($this->get('container.attrs', [])); ?>>
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
<?php $this->after();