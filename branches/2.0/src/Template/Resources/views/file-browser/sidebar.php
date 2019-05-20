<?php
/**
 * Navigateur de fichier > Barre latérale.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\FileBrowser\Viewer $this
 * @var tiFy\Template\Templates\FileBrowser\Contracts\FileInfo $file
 */
?>
<div class="Browser-sidebarInfos" data-control="file-browser.sidebar.file-infos">
    <h3 class="Browser-title"><?php _e('Élèment sélectionné', 'tify'); ?></h3>
    <?php $this->insert('file-infos', compact('file')); ?>
</div>

<h3 class="Browser-title"><?php _e('Répertoire courant', 'tify'); ?></h3>

<ul class="Browser-sidebarActions">
    <li class="Browser-sidebarAction Browser-sidebarAction--create">
        <?php $this->insert('action-create', compact('file')); ?>
        <div class="Browser-sidebarActionButton">
            <a href="#"
               class="Browser-button Browser-button--toggle"
               data-control="file-browser.action.toggle"
               data-action="create"
            ><?php _e('Créer un dossier', 'tify'); ?></a>
        </div>
    </li>
    <li class="Browser-sidebarAction Browser-sidebarAction--upload">
        <?php $this->insert('action-upload', compact('file')); ?>
        <div class="Browser-sidebarActionButton">
            <a href="#"
               class="Browser-button Browser-button--toggle"
               data-control="file-browser.action.toggle"
               data-action="upload"
            ><?php _e('Ajouter des fichiers', 'tify'); ?></a>
        </div>
    </li>
</ul>
