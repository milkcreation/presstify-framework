<?php
/**
 * @var tiFy\Partial\TemplateController $this
 * @var string $html_attrs Liste des attributs HTML de la balise du conteneur.
 * @var array $parts Liste des éléments contenus dans le fil d'ariane
 */
?>

<ol <?php echo $html_attrs; ?>>
    <?php foreach ($parts as $part) : echo $part; endforeach;?>
</ol>