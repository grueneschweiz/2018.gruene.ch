<?php

$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://grueneschweiz.github.io/2018.gruene.ch/theme/update.json',
	get_template_directory() . '/functions.php',
	'les-verts'
);
