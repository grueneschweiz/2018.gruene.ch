<?php

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$themeUpdateChecker = PucFactory::buildUpdateChecker(
	'https://grueneschweiz.github.io/2018.gruene.ch/theme/update.json',
	get_stylesheet_directory(), // Use get_stylesheet_directory() for themes
	'les-verts'
);
