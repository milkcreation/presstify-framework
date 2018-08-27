<?php
/**
 * @var tiFy\Components\TabMetaboxes\PostType\ImageGallery\TemplateController $this
 */
?>

<li class="tiFyTabMetaboxPostTypeImageGallery-item tiFy-CardItem">
    <div class="tiFy-Card">
        <img src="<?php echo $this->get('src', ''); ?>" class="tiFyTabMetaboxPostTypeImageGallery-itemThumbnail tiFy-CardImg" />
        <input type="hidden" name="<?php echo $this->get('name', ''); ?>" value="<?php echo $this->get('id', 0); ?>" />
        <a href="#remove" class="tiFyTabMetaboxPostTypeImageGallery-itemRemove tiFy-Button--remove"></a>
        <input type="text" class="tiFyTabMetaboxPostTypeImageGallery-itemOrder tiFy-CardOrder" value="<?php echo $this->get('order', 0); ?>" size="1" readonly />
    </div>
</li>