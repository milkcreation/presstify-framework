<?php
/**
 * Navigateur de fichier > Bouton de bascule du type de vue.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\FileBrowser\Viewer $this
 */
?>
<ul class="Browser-switcher" data-control="file-browser.switcher">
    <li class="Browser-switch Browser-switch--grid selected">
        <a href="#" class="Browser-switchLink" data-control="file-browser.view.toggle" data-view="grid">
            <?php echo $this->getIcon('grid'); ?>
        </a>
    </li>

    <li class="Browser-switch Browser-switch--list">
        <a href="#" class="Browser-switchLink" data-control="file-browser.view.toggle" data-view="list">
            <?php echo $this->getIcon('list'); ?>
        </a>
    </li>
</ul>