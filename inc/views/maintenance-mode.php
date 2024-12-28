<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<title><?php wp_title(); ?></title>
	<?php wp_head(); ?>
	<style>
	.mm-wrapper {
		display: flex;
		width: 100vw;
		height: 100vh;
		justify-content: center;
		align-items: center;
		flex-flow: column wrap;
		font-size: 2rem;
	}
	</style>
</head>
<body>
	<div class="mm-wrapper">
		<h1><?php esc_html_e('Howdy!', 'maintenance-mode'); ?></h1>
		<p><?php esc_html_e("We're just freshening things up a bit; back in a few!", 'maintenance-mode'); ?></p>
	</div>
	<?php wp_footer(); ?>
</body>
</html>
