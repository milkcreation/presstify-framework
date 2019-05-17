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
    <ul class="Browser-finfoAttrs">
        <li class="Browser-finfoAttr Browser-finfoAttr--name">
            <label><?php _e('Nom :', 'tify'); ?></label>
            <span><?php echo $file->getBasename(); ?></span>
        </li>
        <li class="Browser-finfoAttr Browser-finfoAttr--type">
            <label><?php _e('Type :', 'tify'); ?></label>
            <span><?php echo $file->getHumanType(); ?></span>
        </li>
        <li class="Browser-finfoAttr Browser-finfoAttr--ext">
            <label><?php _e('Type de médias :', 'tify'); ?></label>
            <span><?php echo $file->getMimetype(); ?></span>
        </li>
        <li class="Browser-finfoAttr Browser-finfoAttr--size">
            <label><?php _e('Taille :', 'tify'); ?></label>
            <span><?php echo $file->getHumanSize(); ?></span>
        </li>
        <li class="Browser-finfoAttr Browser-finfoAttr--date">
            <label><?php _e('Date :', 'tify'); ?></label>
            <span><?php echo $file->getHumanDate('d/m/Y'); ?></span>
        </li>
        <?php /* if ($file->isLocal()) : ?>
            <li class="Browser-finfoAttr Browser-finfoAttr--ctime">
                <label><?php _e('Création :', 'tify'); ?></label>
                <span><?php echo $file->getHumanDate('d/m/Y'); ?></span>
            </li>
            <li class="Browser-finfoAttr Browser-finfoAttr--mtime">
                <label><?php _e('Modification :', 'tify'); ?></label>
                <span><?php echo $file->getMTime(); ?></span>
            </li>
            <li class="Browser-finfoAttr Browser-finfoAttr--owner">
                <label><?php _e('Propriétaire :', 'tify'); ?></label>
                <span><?php echo $file->getOwner(); ?></span>
            </li>
            <li class="Browser-finfoAttr Browser-finfoAttr--group">
                <label><?php _e('Groupe :', 'tify'); ?></label>
                <span><?php echo $file->getGroup(); ?></span>
            </li>
        <?php endif; */ ?>
    </ul>
    <?php if (!$file->isRoot()) : ?>
        <ul class="Browser-finfoActions">
            <li class="Browser-finfoAction Browser-finfoAction--newname">
                <?php $this->insert('action-newname', compact('file')); ?>
                <div class="Browser-finfoActionButton">
                    <a href="#"
                       class="Browser-button Browser-button--toggle"
                       data-control="file-browser.button.toggle"
                       data-target="[data-control='file-browser.action.newname']"
                    ><?php _e('Renommer', 'tify'); ?></a>
                </div>
            </li>

            <li class="Browser-finfoAction Browser-finfoAction--delete">
                <?php $this->insert('action-delete', compact('file')); ?>
                <div class="Browser-finfoActionButton">
                    <a href="#"
                       class="Browser-button Browser-button--toggle"
                       data-control="file-browser.button.toggle"
                       data-target="[data-control='file-browser.action.delete']"
                    ><?php _e('Supprimer', 'tify'); ?></a>
                </div>
            </li>
        </ul>
    <?php endif; ?>
</div>