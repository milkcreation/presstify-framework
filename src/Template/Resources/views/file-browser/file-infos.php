<?php
/**
 * Navigateur de fichier > Fichier.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\FileBrowser\Viewer $this
 * @var tiFy\Template\Templates\FileBrowser\Contracts\FileInfo $file
 */
?>
<div class="Browser-fileInfos">
    <div class="Browser-fileInfosPreview">
        <?php echo $file->getIcon(); ?>
    </div>
    <ul class="Browser-fileInfosAttrs">
        <li class="Browser-fileInfosAttr Browser-fileInfosAttr--name">
            <label><?php _e('Nom :', 'tify'); ?></label>
            <span><?php echo $file->getBasename(); ?></span>
        </li>
        <li class="Browser-fileInfosAttr Browser-fileInfosAttr--type">
            <label><?php _e('Type :', 'tify'); ?></label>
            <span><?php echo $file->getHumanType(); ?></span>
        </li>
        <li class="Browser-fileInfosAttr Browser-fileInfosAttr--ext">
            <label><?php _e('Type de médias :', 'tify'); ?></label>
            <span><?php echo $file->getMimetype(); ?></span>
        </li>
        <li class="Browser-fileInfosAttr Browser-fileInfosAttr--size">
            <label><?php _e('Taille :', 'tify'); ?></label>
            <span><?php echo $file->getHumanSize(); ?></span>
        </li>
        <li class="Browser-fileInfosAttr Browser-fileInfosAttr--date">
            <label><?php _e('Date :', 'tify'); ?></label>
            <span><?php echo $file->getHumanDate('d/m/Y'); ?></span>
        </li>
        <?php /* if ($file->isLocal()) : ?>
            <li class="Browser-fileInfosAttr Browser-fileInfosAttr--ctime">
                <label><?php _e('Création :', 'tify'); ?></label>
                <span><?php echo $file->getHumanDate('d/m/Y'); ?></span>
            </li>
            <li class="Browser-fileInfosAttr Browser-fileInfosAttr--mtime">
                <label><?php _e('Modification :', 'tify'); ?></label>
                <span><?php echo $file->getMTime(); ?></span>
            </li>
            <li class="Browser-fileInfosAttr Browser-fileInfosAttr--owner">
                <label><?php _e('Propriétaire :', 'tify'); ?></label>
                <span><?php echo $file->getOwner(); ?></span>
            </li>
            <li class="Browser-fileInfosAttr Browser-fileInfosAttr--group">
                <label><?php _e('Groupe :', 'tify'); ?></label>
                <span><?php echo $file->getGroup(); ?></span>
            </li>
        <?php endif; */ ?>
    </ul>
    <?php if (!$file->isRoot()) : ?>
        <ul class="Browser-fileInfosActions">
            <li class="Browser-fileInfosAction Browser-fileInfosAction--rename">
                <?php $this->insert('form-newname', compact('file')); ?>
                <a href="#" class="Browser-button"><?php _e('Renommer', 'tify'); ?></a>
            </li>

            <li class="Browser-fileInfosAction Browser-fileInfosAction--delete">
                <?php $this->insert('form-delete', compact('file')); ?>
                <a href="#" class="Browser-button">
                    <?php _e('Supprimer', 'tify'); ?>
                </a>
            </li>
        </ul>
    <?php endif; ?>
</div>