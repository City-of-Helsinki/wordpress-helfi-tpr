<?php

namespace CityOfHelsinki\WordPress\TPR\Views\Metabox\UnitData;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use CityOfHelsinki\WordPress\TPR\Cpt as Plugin;

?>
    <nav class="nav-tab-wrapper">
		<?php
			foreach( $data['languages'] as $lang_code => $language ) {
				$classes = 'nav-tab';
				if ( $lang_code === $data['active_language'] ) {
					$classes .= ' nav-tab-active';
				}

				printf(
					'<a href="#" class="%s" data-container="%s-tpr-data">
						%s
					</a>',
					$classes,
					\esc_attr( $language['name'] ),
					\esc_html( $language['label'] )
				);
			}
		?>
    </nav>

	<?php
		if ( $data['unit'] ) {

			foreach( $data['languages'] as $lang_code => $language ) {
				$classes = sprintf( 'post-tpr-data %s-tpr-data', $language['name'] );
				if ( $lang_code === $data['active_language'] ) {
					$classes .= ' active';
				}

				printf( '<div class="%s">', \esc_attr( $classes ) );

		        Plugin\render_unit_data_row(
					__('Image', 'helsinki-tpr'),
					array( $data['unit']->html_img( $lang_code ) ),
					'tpr-img'
				);

				Plugin\render_unit_data_row(
					__('Name', 'helsinki-tpr'),
					array( $data['unit']->name( $lang_code ) )
				);

		        Plugin\render_unit_data_row(
					__('Street Address', 'helsinki-tpr'),
					array(
						$data['unit']->street_address( $lang_code ),
						$data['unit']->address_zip(),
						$data['unit']->address_city( $lang_code ),
						$data['unit']->get_service_map_link()
					)
				);

		        Plugin\render_unit_data_row(
					__('Email', 'helsinki-tpr'),
					array( $data['unit']->email() )
				);

		        Plugin\render_unit_data_row(
					__('Phone', 'helsinki-tpr'),
					array( $data['unit']->phone() )
				);

		        Plugin\render_unit_data_row(
					__('Open hours', 'helsinki-tpr'),
					$data['unit']->open_hours_html( $lang_code )
				);

		        Plugin\render_unit_data_row(
					__('Service language', 'helsinki-tpr'),
					$data['unit']->available_languages()
				);

		        Plugin\render_unit_data_row(
					__('Website', 'helsinki-tpr'),
					array( $data['unit']->website_url( $lang_code ) )
				);

		        Plugin\render_unit_data_row(
					__('Postal Address', 'helsinki-tpr'),
					array( $data['unit']->postal_address( $lang_code ) )
				);

		        Plugin\render_unit_data_row(
					__('How to get here', 'helsinki-tpr'),
					array( $data['unit']->get_hsl_route_link() )
				);

		        Plugin\render_unit_data_row(
					__('Additional information', 'helsinki-tpr'),
					$data['unit']->additional_info_html( $lang_code )
				);

				echo '</div>';
			}

		}
	?>
  <?php
