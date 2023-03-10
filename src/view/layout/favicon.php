<?php $favicon_path = isset($favicon_path) ? $favicon_path : join(DIRECTORY_SEPARATOR, array(ASSET_PATH, 'favicon')); ?>
<!--Favicon-->
		<link rel="apple-touch-icon" sizes="180x180" href="<?= join(DIRECTORY_SEPARATOR, array($favicon_path, 'apple-touch-icon.png')) ?>">
		<link rel="icon" type="image/png" sizes="32x32" href="<?= join(DIRECTORY_SEPARATOR, array($favicon_path, 'favicon-32x32.png')) ?>">
		<link rel="icon" type="image/png" sizes="16x16" href="<?= join(DIRECTORY_SEPARATOR, array($favicon_path, 'favicon-16x16.png')) ?>">
		<link rel="manifest" href="<?= join(DIRECTORY_SEPARATOR, array($favicon_path, 'site.webmanifest')) ?>">
		<link rel="mask-icon" href="<?= join(DIRECTORY_SEPARATOR, array($favicon_path, 'safari-pinned-tab.svg'))?>" color="#5bbad5">
		<meta name="msapplication-TileColor" content="#da532c">
		<meta name="theme-color" content="#ffffff">