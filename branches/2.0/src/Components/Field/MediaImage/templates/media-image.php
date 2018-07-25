<?php
/**
 * @var tiFy\Apps\Templates\TemplateBaseController $this
 */
?>

<div <?php echo $this->htmlAttrs($container_attrs); ?>>
    <a href="#tiFyField-MediaImageAdd--<?php echo $this->getIndex(); ?>" id="tiFyField-MediaImageAdd--<?php echo $this->getIndex(); ?>" class="tiFyField-MediaImageAdd" title="<?php _e('Modification de l\'image', 'tify'); ?>"
        style="background-image:url(<?php echo $value_img;?>); width:100%; padding-top: <?php echo 100*($this->get('height')/$this->get('width')) . "%;"; ?><?php echo $editable ? 'cursor:pointer;' : 'cursor:default;'; ?>">
        <?php if ($editable) : ?>
        <i class="tiFyField-MediaImageAddIco"></i>
        <?php endif; ?>
        <span class=""></span>
    </a>

    <?php if ($info_txt) : ?>
        <span class="tiFyField-MediaImageSize"><?php echo $info_txt; ?></span>
    <?php endif; ?>

    <?php if ($content) : ?>
        <div class="tiFyField-MediaImageContent"><?php echo $content; ?></div>
    <?php endif; ?>

    <input type="hidden" class="tiFyField-MediaImageInput" name="<?php echo $name; ?>" value="<?php echo $value; ?>"/>
    <?php /*
    <a href="#tiFyField-MediaImageReset--<?php echo $index; ?>" id="tiFyField-MediaImageReset--<?php echo $index; ?>" title="<?php _e('Rétablir l\'image originale', 'tify'); ?>"
        class="tiFyField-MediaImageReset tify_button_remove" style="display:<?php echo ($value && ($value_img != $default_img)) ? 'inherit' : 'none'; ?>">
    </a>
 */ ?>
</div>
