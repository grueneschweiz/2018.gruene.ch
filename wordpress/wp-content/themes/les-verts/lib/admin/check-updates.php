<?php

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

PucFactory::buildUpdateChecker(
	'https://grueneschweiz.github.io/2018.gruene.ch/theme/update.json',
	get_template_directory() . '/functions.php',
	'les-verts'
);
