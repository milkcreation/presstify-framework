<?php
/**
 * @var tiFy\Contracts\View\ViewController $this
 */
?>

<table class="form-table">
    <tbody>
    <?php
    /** @var tiFy\PageHook\PageHookItemInterface $item */
    foreach ($this->get('items', []) as $item) : ?>
        <tr>
            <th><?php echo $item->getTitle(); ?></th>
            <td>
                <?php
                \wp_dropdown_pages(
                    [
                        'name'             => $item->getOptionName(),
                        'post_type'        => $item->getObjectName(),
                        'selected'         => $item->getId(),
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