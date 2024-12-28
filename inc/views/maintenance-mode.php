<?php

declare(strict_types=1);

use Syntatis\FeatureFlipper\Helpers\Option;

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<title><?php wp_title(); ?></title>
	<?php wp_head(); ?>
	<style>
	#syntatis-feature-flipper-site-maintenance {
		display: flex;
		width: 100vw;
		height: 100vh;
		padding-inline: 20vw;
		box-sizing: border-box;
		text-align: center;
		justify-content: center;
		align-items: center;
		flex-flow: column wrap;
		font-size: 2rem;
	}
	</style>
</head>
<body>
	<div id="syntatis-feature-flipper-site-maintenance">
		<h1><?php echo esc_html(Option::get('site_maintenance_args')['headline'] ?? ''); ?></h1>
		<?php echo wp_kses_post(wpautop(Option::get('site_maintenance_args')['description'] ?? '')); ?>
	</div>
	<?php wp_footer(); ?>
</body>
</html>
