<?php
/**
 * Navigateur de fichier > Fichier.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\FileBrowser\Viewer $this
 * @var tiFy\Template\Templates\FileBrowser\Contracts\FileInfo $file
 */
?>
<div class="Browser-finfo">
    <div class="Browser-finfoPreview">
        <?php echo $file->getIcon(); ?>
    </div>
    <?php if ($file->isFile()) : ?>
    <ul class="Browser-finfoHandlers">
        <li class="Browser-finfoHandler Browser-finfoHandler--download">
            <a href="<?php echo $file->getDownloadUrl(); ?>"
               class="Browser-finfoHandlerLink"
               data-control="file-browser.handler.download"
            >
                <?php echo $this->getIcon('download'); ?><?php _e('Télécharger', 'tify'); ?>
            </a>
        </li>
        <?php /*
        <li class="Browser-finfoHandler Browser-finfoHandler--preview">
            <a href="#"
               class="Browser-finfoHandlerLink"
               data-control="file-browser.handler.preview"
            >
                <?php echo $this->getIcon('preview'); ?><?php _e('Prévisualiser', 'tify'); ?>
            </a>
        </li>
        */ ?>
    </ul>
    <?php endif; ?>
    <ul class="Browser-finfoAttrs">
        <li class="Browser-finfoAttr Browser-finfoAttr--name">
            <label class="Browser-finfoAttrLabel"><?php _e('Nom :', 'tify'); ?></label>
            <span class="Browser-finfoAttrValue"><?php echo $file->getBasename(); ?></span>
        </li>
        <li class="Browser-finfoAttr Browser-finfoAttr--type">
            <label class="Browser-finfoAttrLabel"><?php _e('Type :', 'tify'); ?></label>
            <span class="Browser-finfoAttrValue"><?php echo $file->getHumanType(); ?></span>
        </li>
        <li class="Browser-finfoAttr Browser-finfoAttr--ext">
            <label class="Browser-finfoAttrLabel"><?php _e('Type de médias :', 'tify'); ?></label>
            <span class="Browser-finfoAttrValue"><?php echo $file->getMimetype(); ?></span>
        </li>
        <li class="Browser-finfoAttr Browser-finfoAttr--size">
            <label class="Browser-finfoAttrLabel"><?php _e('Taille :', 'tify'); ?></label>
            <span class="Browser-finfoAttrValue"><?php echo $file->getHumanSize(); ?></span>
        </li>
        <li class="Browser-finfoAttr Browser-finfoAttr--date">
            <label class="Browser-finfoAttrLabel"><?php _e('Date :', 'tify'); ?></label>
            <span class="Browser-finfoAttrValue"><?php echo $file->getHumanDate('d/m/Y'); ?></span>
        </li>
        <?php /* if ($file->isLocal()) : ?>
            <li class="Browser-finfoAttr Browser-finfoAttr--ctime">
                <label class="Browser-finfoAttrLabel"><?php _e('Création :', 'tify'); ?></label>
                <span class="Browser-finfoAttrValue"><?php echo $file->getHumanDate('d/m/Y'); ?></span>
            </li>
            <li class="Browser-finfoAttr Browser-finfoAttr--mtime">
                <label class="Browser-finfoAttrLabel"><?php _e('Modification :', 'tify'); ?></label>
                <span class="Browser-finfoAttrValue"><?php echo $file->getMTime(); ?></span>
            </li>
            <li class="Browser-finfoAttr Browser-finfoAttr--owner">
                <label class="Browser-finfoAttrLabel"><?php _e('Propriétaire :', 'tify'); ?></label>
                <span class="Browser-finfoAttrValue"><?php echo $file->getOwner(); ?></span>
            </li>
            <li class="Browser-finfoAttr Browser-finfoAttr--group">
                <label class="Browser-finfoAttrLabel"><?php _e('Groupe :', 'tify'); ?></label>
                <span class="Browser-finfoAttrValue"><?php echo $file->getGroup(); ?></span>
            </li>
        <?php endif; */ ?>
    </ul>
    <?php if (!$file->isRoot()) : ?>
        <ul class="Browser-finfoActions">
            <li class="Browser-finfoAction Browser-finfoAction--rename">
                <?php $this->insert('action-rename', compact('file')); ?>
                <div class="Browser-finfoActionButton">
                    <a href="#"
                       class="Browser-button Browser-button--toggle"
                       data-control="file-browser.action.toggle"
                       data-action="rename"
                    ><?php _e('Renommer', 'tify'); ?></a>
                </div>
            </li>

            <li class="Browser-finfoAction Browser-finfoAction--delete">
                <?php $this->insert('action-delete', compact('file')); ?>
                <div class="Browser-finfoActionButton">
                    <a href="#"
                       class="Browser-button Browser-button--toggle"
                       data-control="file-browser.action.toggle"
                       data-action="delete"
                    ><?php _e('Supprimer', 'tify'); ?></a>
                </div>
            </li>
        </ul>
    <?php endif; ?>
</div>