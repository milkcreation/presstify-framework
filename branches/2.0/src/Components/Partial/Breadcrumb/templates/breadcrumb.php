<?php
/**
 * @var tiFy\Partial\TemplateController $this
 */
?>

<ol <?php echo $html_attrs; ?>>
    <?php foreach ($parts as $part) : echo $part; endforeach;?>
</ol>