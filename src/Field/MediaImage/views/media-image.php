<?php
/**
 * @var tiFy\Field\FieldView $this
 */
?>

<?php $this->before(); ?>

    <div <?php $this->attrs(); ?>>
        <a href="#tiFyField-MediaImageAdd--<?php echo $this->getIndex(); ?>"
           id="tiFyField-MediaImageAdd--<?php echo $this->getIndex(); ?>" class="tiFyField-MediaImageAdd"
           title="<?php _e('Modification de l\'image', 'tify'); ?>"
           style="background-image:url(<?php echo $this->get('value_img', ''); ?>);
                   width:100%;
                   padding-top: <?php echo 100 * ($this->get('height') / $this->get('width')) . "%;"; ?>
                <?php echo $this->get('editable', true) ? 'cursor:pointer;' : 'cursor:default;'; ?>"
        >
            <?php if ($this->get('editable', true)) : ?>
                <i class="tiFyField-MediaImageAddIco"></i>
            <?php endif; ?>
        </a>

        <?php if ($info_txt = $this->get('info_txt', '')) : ?>
            <span class="tiFyField-MediaImageSize"><?php echo $info_txt; ?></span>
        <?php endif; ?>

        <?php if ($content = $this->get('content', '')) : ?>
            <div class="tiFyField-MediaImageContent"><?php echo $content; ?></div>
        <?php endif; ?>

        <input type="hidden" class="tiFyField-MediaImageInput" name="<?php echo $this->get('name', ''); ?>"
               value="<?php echo $this->getValue(); ?>"/>

        <?php if ($this->get('removable', true)) : ?>
            <a href="#<?php $this->get('attrs.id', ''); ?>" class="tiFyField-MediaImageRemove tiFy-Button--remove"></a>
        <?php endif; ?>
    </div>

<?php $this->after();