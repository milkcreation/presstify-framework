<?php
/**
 * Vue filtrées
 *
 * @var tiFy\Components\Layout\ListTable\TemplateController $this
 */
?>

<?php if ($viewFilters = $this->getViewFilters()) :?>
<ul class='subsubsub'>
    <?php foreach($viewFilters as $name => $filter) : ?>
    <li class="<?php echo $name; ?>"><?php echo $filter; ?></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>