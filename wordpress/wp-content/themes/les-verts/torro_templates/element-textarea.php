<?php
/**
 * Template: element-textarea.php
 *
 * Available data: $id, $container_id, $label, $sort, $type, $value, $input_attrs, $label_required, $label_attrs, $wrap_attrs, $description, $description_attrs, $errors, $errors_attrs, $before, $after
 *
 * @package TorroForms
 * @since 1.0.0
 */

$wrap_attrs['class'] .= ' a-input';
$input_attrs['class'] .= ' a-input__field';
$label_attrs['class'] .= ' a-input__label';

if ($label_required) {
	$label_attrs['class'] .= ' a-input__label--required';
}

include WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'torro-forms' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'element-textarea.php';
