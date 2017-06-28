<?php

	/*
	Plugin Name: Donut Chart
	Description: A customizable donut chart maker
	Version: 0.1.0
	Author: Emily Kauffman
	Author URI: http://intrepidem.net
	License: MIT
	*/

	function dc_load_styles () {
		wp_enqueue_style( 'dc_css', plugins_url( 'donut-styles.css' ) );
	}

	add_action( 'admin_head', 'dc_load_styles' );

	//keep location of script set to false - must load first
	function dc_load_d3_scripts () {
		wp_enqueue_script( 'dc_d3_script', plugins_url('donut-chart/libs/d3/d3.v4.min.js') , null, '', false );
		wp_enqueue_script( 'dc_d3-interpolate-script', plugins_url('donut-chart/libs/d3-interpolate/d3-interpolate.v1.min.js') , null, '', false );
		wp_enqueue_script( 'dc_d3-ease-script', plugins_url('donut-chart/libs/d3-ease/d3-ease.v1.min.js') , null, '', false );
		wp_enqueue_script( 'dc_d3-transition-script', plugins_url('donut-chart/libs/d3-transition/d3-transition.v1.min.js') , null, '', false );
	}

	add_action( 'wp_enqueue_scripts', 'dc_load_d3_scripts' );

	//WP_Widget: https://developer.wordpress.org/reference/classes/wp_widget
	class DonutChart extends WP_Widget {

		//constructor for child class
		function __construct() {

			/*
			 Need to call parent's (WP_Widget) constructor because it is not
			 alled by default
			 WP_Widget::__construct( string $id_base, string $name, array $widget_options = array(), array $control_options = array() )
			 Object in PHP -> like a class
			 Array - can access properties by name
			 "::" - indicates calling it statically
			*/
			parent::__construct(
				'DonutChart',
				__('Donut Chart', 'dx-text_domain'),
				array( 'description' => __('A widget that shows a donut chart ', 'dx-text_domain') )
			);

			//self::function_name -> static way to call within a class
			//$this - dynamic way
		}

		//content that shows in public view
		public function widget ( $args, $instance ) {

			echo $args['before_widget'];

			echo "<div id='donut-container'>";
			echo "</div><!-- /donut-container -->";

			echo "<script>";
			echo "window.CHART_DATA = {};";
			echo "window.CHART_DATA.chartLabel = '" . $instance['chartLabel'] . "';";
			echo "window.CHART_DATA.value = 0;";
			echo "window.CHART_DATA.options = {};";
			echo "window.CHART_DATA.options.postUnitLabel = '%';";
			echo "window.CHART_DATA.options.configFile = '" . plugins_url('donut-chart/serverStatus.csv') . "';";

			echo "</script>";

			echo "<script src=" . plugins_url("donut-chart/donutChart.js") . "></script>";

			//echo "Home of a future donut chart!";
			echo $args['after_widget'];

		}

		public function form ( $instance ) {

			$chartLabel = ! empty( $instance['chartLabel'] ) ? $instance['chartLabel'] : '';
			//$filename = ! empty( $instance['filename'] ) ? $instance['filename'] : '';
			$configFile = ! empty( $instance['filename'] ) ? $instance['filename'] : '';

			?>

				<!-- Html template here -->
				<p>
					<!-- Chart Label -->
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( 'chartLabel' )); ?>"><?php _e( esc_attr( 'Chart Label:' ) ); ?></label>
						<input
							class="widefat"
							id="<?php echo esc_attr( $this->get_field_id( 'chartLabel' )); ?>"
							name="<?php echo $this->get_field_name( 'chartLabel') ?>"
							type="text"
							value="<?php echo esc_attr( $chartLabel ); ?>"
							placeholder="Ex. Server Status">
					</p>

					<!-- Config file -->
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( 'configFile' )); ?>"><?php _e( esc_attr( 'File in Media Folder:' ) ); ?></label>
						<input
							class="widefat"
							accepts=".json"
							id="<?php echo esc_attr( $this->get_field_id( 'configFile' )); ?>"
							name="<?php echo $this->get_field_name( 'configFile') ?>"
							type="file"
							value="<?php echo esc_attr( $configFile ); ?>">
					</p>

				</p>


			<?php

		}

		public function update ( $new_instance, $old_instance ) {

			$instance = $old_instance;

			$instance['chartLabel'] = ( ! empty( $new_instance['chartLabel'] ) ) ? strip_tags( $new_instance['chartLabel'] ) : 'Untitled';
			// $instance['filename'] = ( ! empty( $new_instance['filename'] ) ) ? strip_tags( $new_instance['filename'] ) : '';
			$instance['configFile'] = ( ! empty( $new_instance['configFile'] ) ) ? strip_tags( $new_instance['configFile'] ) : '';

			// $instance['slice1Label'] = ( ! empty( $new_instance['slice1Label'] ) ) ? strip_tags( $new_instance['slice1Label'] ) : 'Slice 1';
			// $instance['slice1Color'] = ( ! empty( $new_instance['slice1Color'] ) ) ? strip_tags( $new_instance['slice1Color'] ) : 'red';
			// $instance['slice1Value'] = ( ! empty( $new_instance['slice1Value'] ) ) ? strip_tags( $new_instance['slice1Value'] ) : 75;

			// $instance['slice2Label'] = ( ! empty( $new_instance['slice2Label'] ) ) ? strip_tags( $new_instance['slice2Label'] ) : 'Untitled';
			// $instance['slice2Color'] = ( ! empty( $new_instance['slice2Color'] ) ) ? strip_tags( $new_instance['slice2Color'] ) : 'yellow';
			// $instance['slice2Value'] = ( ! empty( $new_instance['slice2Value'] ) ) ? strip_tags( $new_instance['slice2Value'] ) : 20;

			// $instance['slice3Label'] = ( ! empty( $new_instance['slice3Label'] ) ) ? strip_tags( $new_instance['slice3Label'] ) : 'Untitled';
			// $instance['slice3Color'] = ( ! empty( $new_instance['slice3Color'] ) ) ? strip_tags( $new_instance['slice3Color'] ) : 'red';
			// $instance['slice3Value'] = ( ! empty( $new_instance['slice3Value'] ) ) ? strip_tags( $new_instance['slice3Value'] ) : 5;


			return $instance;

		}

	}

	function register_donut_chart () {
		register_widget( 'DonutChart' );
	}

	add_action( 'widgets_init', 'register_donut_chart' );

?>