<?php 
add_action('wp_head', function () {

	$settings = get_option('logestay_settings', []);
	if (!is_array($settings)) return;

	$primary   = trim($settings['logestay_color_primary'] ?? '');
	$secondary = trim($settings['logestay_color_secondary'] ?? '');

	// Nothing to output
	if (!$primary && !$secondary) {
		return;
	}

	echo "<style id='logestay-dynamic-colors'>\n";

	if ($primary) {
		?>
			.ring-amber-500 {
			  --tw-ring-color: <?php echo $primary; ?>;
			}
			.bg-amber-500, .primary_btn{
				background-color:<?php echo $primary; ?>;
			}
		<?php 
	}

	if ($secondary) {
		?>
			.secondary_btn{
				background-color:<?php echo $secondary; ?>;
			}
			.selected_item{
				background-color:<?php echo $secondary; ?>;
			}
		<?php 
	}

	echo "</style>\n";

});