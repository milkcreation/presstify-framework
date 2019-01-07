<?php
/**
 * Entête de la table.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\View\Pattern\ListTable\Viewer\Viewer $this
 * @var string $attrs Liste des attributs de blise HTML.
 * @var string $content Contenu.
 */
?>
<th <?php echo $this->get('attrs', ''); ?>><?php echo $this->get('content'); ?></th>
