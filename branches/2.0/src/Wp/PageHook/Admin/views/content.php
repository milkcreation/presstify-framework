<?php
/**
 * @var tiFy\Contracts\View\ViewController $this
 * @var tiFy\Contracts\Wp\PageHookItem $item
 */
?>
<table class="form-table">
    <tbody>
    <?php
    foreach ($this->get('items', []) as $item) : ?>
        <tr>
            <th><?php echo $item->getTitle(); ?></th>
            <td>
                <?php
                wp_dropdown_pages(
                    [
                        'name'             => $item->getOptionName(),
                        'post_type'        => $item->getObjectName(),
                        'selected'         => $item->post() ? $item->post()->getId() : 0,
                        'sort_column'      => $item->get('listorder'),
                        'show_option_none' => $item->get('show_option_none'),
                    ]
                );
                ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>