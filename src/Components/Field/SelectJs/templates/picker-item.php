<?php
/**
 * @var \tiFy\Field\TemplateController $this
 * @var string $label Intitulé de l'élément
 * @var mixed $value Valeur de l'élément
 * @var int $index Indice de l'élément
 * @var bool $disabled Etat d'activation de l'élément
 */
?>

<li data-label="<?php echo $label; ?>"
    data-value="<?php echo $value; ?>"
    data-index="<?php echo $index; ?>"
    aria-disabled="<?php echo $disabled; ?>"
>
    <?php echo $label; ?>
</li>
