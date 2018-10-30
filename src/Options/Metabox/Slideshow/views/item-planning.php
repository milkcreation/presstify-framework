<?php
/**
 * @var \tiFy\Contracts\Views\ViewInterface $this
 */
?>

<div class="MetaboxOptions-slideshowItemInput MetaboxOptions-slideshowItemInput--title">
    <h3><?php _e('Programmation', 'tify'); ?></h3>
    $output .= "\n\t\t\t<div class=\"start_datetime\">";
        $output .= "\n\t\t\t\t<label>";
            $output .= "\n\t\t\t\t\t<input type=\"checkbox\" data-hide_unchecked=\".planning-start\" name=\"{$name}[{$index}][planning][from]\" value=\"1\" " . checked(1,
            (isset($slide['planning']['from']) ? $slide['planning']['from'] : 0),
            false) . " autocomplete=\"off\"/>";
            $output .= "\n\t\t\t\t\t" . __('A partir du', 'tify');
            $output .= "\n\t\t\t\t</label>";
        $output .= "\n\t\t\t\t" . tify_control_touch_time([
        'name'            => "{$name}[{$index}][planning][start]",
        'container_class' => 'planning-start',
        'value'           => $slide['planning']['start'],
        'echo'            => false
        ]);
        $output .= "\n\t\t\t</div>";
    $output .= "\n\t\t\t<div class=\"end_datetime\">";
        $output .= "\n\t\t\t\t<label>";
            $output .= "\n\t\t\t\t\t<input type=\"checkbox\" data-hide_unchecked=\".planning-end\" name=\"{$name}[{$index}][planning][to]\" value=\"1\" " . checked(1,
            (isset($slide['planning']['to']) ? $slide['planning']['to'] : 0), false) . " autocomplete=\"off\"/>";
            $output .= "\n\t\t\t\t\t" . __('Jusqu\'au', 'tify');
            $output .= "\n\t\t\t\t</label>";
        $output .= "\n\t\t\t\t" . tify_control_touch_time([
        'name'            => "{$name}[{$index}][planning][end]",
        'container_class' => 'planning-end',
        'value'           => $slide['planning']['end'],
        'echo'            => false
        ]);
        $output .= "\n\t\t\t</div>";
</div>