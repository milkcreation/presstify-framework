<?php
/**
 * @var \tiFy\App\Templates\AppTemplateController $this
 */
?>

<div class="wrap">
    <h2><?php echo $this->get('page_title', ''); ?></h2>

    <form method="post" action="options.php">
        <div style="float:left; width: 100%;">
            <?php \settings_fields($this->get('option_group')); ?>
            <?php \do_settings_sections($this->get('option_group')); ?>
        </div>
        <div class="submit">
            <?php \submit_button(); ?>
        </div>
    </form>
</div>