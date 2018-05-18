<div <?php echo $container_attrs; ?>>
    <a href="#tiFyField-MediaImageAdd--<?php echo $index; ?>" id="tiFyField-MediaImageAdd--<?php echo $index; ?>" class="tiFyField-MediaImageAdd" title="<?php _e('Modification de l\'image', 'tify'); ?>"
        style="background-image:url(<?php echo $value_img;?>);<?php echo $editable ? 'cursor:pointer;' : 'cursor:default;'; ?>">
        <?php if ($editable) : ?>
        <i class="tiFyField-MediaImageAddIco"></i>
        <?php endif; ?>
    </a>
    <?php if ($info_txt) : ?>
        <span class="tiFyField-MediaImageSize"><?php echo $info_txt; ?></span>
    <?php endif; ?>

    <?php if ($content) : ?>
        <div class="tiFyField-MediaImageContent"><?php echo $content; ?></div>
    <?php endif; ?>

    <input type="hidden" class="tiFyField-MediaImageInput" name="<?php echo $name; ?>" value="<?php echo $value; ?>"/>

    <a href="#tiFyField-MediaImageReset--<?php echo $index; ?>" id="tiFyField-MediaImageReset--<?php echo $index; ?>" title="<?php _e('Rétablir l\'image originale', 'tify'); ?>"
        class="tiFyField-MediaImageReset tify_button_remove" style="display:<?php echo ($value && ($value_img != $default_img)) ? 'inherit' : 'none'; ?>">
    </a>
</div>
