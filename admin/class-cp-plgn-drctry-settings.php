<?php
/**
 * The Settings page for this plugin
 *
 * @link       https://www.tukutoi.com/
 * @since      1.3.0
 *
 * @package    Cp_Plgn_Drctry
 * @subpackage Cp_Plgn_Drctry/admin
 */

/**
 * The settings for this plugin.
 *
 * Defines the plugin name, version and
 * the entire options/settings page to define repositories to read.
 *
 * @package    Cp_Plgn_Drctry
 * @subpackage Cp_Plgn_Drctry/admin
 * @author     bedas <hello@tukutoi.com>
 */
class Cp_Plgn_Drctry_Settings {

	/**
	 * Include arbitrary functions
	 */
	use Cp_Plgn_Drctry_Fx;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.3.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The unique prefix of this plugin.
	 *
	 * @since    1.3.0
	 * @access   private
	 * @var      string    $plugin_prefix    The string used to uniquely prefix technical functions of this plugin.
	 */
	private $plugin_prefix;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.3.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The Badge for verified Repos.
	 *
	 * @since    1.3.0
	 * @access   private
	 * @var      string    $version    Badge used for verified Orgs from GitHub.
	 */
	private $verified_badge;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.3.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $plugin_prefix    The unique prefix of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $plugin_prefix, $version ) {

		$this->plugin_name    = $plugin_name;
		$this->plugin_prefix  = $plugin_prefix;
		$this->version        = $version;
		$this->verified_badge = '<svg width="21" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 589.537 589.591"><defs><linearGradient id="a" x1="362.895" y1="362.9" x2="180.38" y2="180.385" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#3ebca6"/><stop offset="0.5" stop-color="#057f99"/><stop offset="1" stop-color="#006b81"/></linearGradient></defs><title>classicpress-logo-feather-gradient-on-transparent</title><path d="M592.578,10.577l-.038-.06c-2.019-3.352-6.948-5.759-10.922-5.535C506.913,9.189,386.56,52.972,381.485,54.838a12.221,12.221,0,0,0-6.729,6.033L357.8,94.742l-12.5-12.5a12.187,12.187,0,0,0-14.368-2.189c-14.317,7.477-86.638,45.826-102.025,64.5-8.932,10.8-15.861,34.556-20.525,55.492l-8.2-16.308a12.169,12.169,0,0,0-8.608-6.556A12.023,12.023,0,0,0,181.115,180c-4.328,3.8-94.612,85.035-75.815,186.327,72.234-102.984,159.046-189.573,301.051-259.295a12.281,12.281,0,0,1,10.86,22.031c-.013,0-.026.012-.039.012-8.259,4.067-16.282,8.209-24.157,12.389-2,1.057-3.968,2.127-5.946,3.2q-9.068,4.907-17.836,9.914c-1.905,1.095-3.8,2.165-5.674,3.26q-22.595,13.172-43.163,27.1c-1.132.759-2.239,1.555-3.384,2.326q-8.955,6.138-17.576,12.414c-1.007.735-2,1.469-3.011,2.215C162.419,300.746,91.529,426.008,6.557,576.248a12.281,12.281,0,0,0,10.7,18.31l.012-.024a12.221,12.221,0,0,0,10.686-6.22c33.834-59.833,65.393-115.61,99.663-167.231,13.558,16.768,32.95,25.549,57.1,25.549,92.883,0,246.27-135.86,261.72-179.62a12.283,12.283,0,0,0-8.6-16L396.9,240.764l89.127-14.852a12.177,12.177,0,0,0,8.957-6.642L593.249,22.757A12.591,12.591,0,0,0,592.578,10.577Z" transform="translate(-4.972 -4.967)" style="fill:url(#a)"/></svg>';

	}

	/**
	 * Custom Setting sections and fields.
	 *
	 * Registers a new setting:
	 * - `cp_dir_opts_options`
	 *
	 * Adds two sections:
	 * - `cp_dir_opts_section_external_repos`
	 * - `cp_dir_opts_section_github_token`
	 * Adds four setting fields:
	 *
	 * - `cp_dir_opts_exteranal_org_repos`
	 * - `cp_dir_opts_exteranal_user_repos`
	 * - `cp_dir_opts_exteranal_repos`
	 * - `cp_dir_opts_section_github_token`
	 *
	 * @since 1.3.0
	 */
	public function settings_init() {

		register_setting( 'cp_dir_opts', 'cp_dir_opts_options' );

		add_settings_section(
			'cp_dir_opts_section_external_repos',
			__( 'External ClassicPress Repositories', 'cp-plgn-drctry' ),
			array( $this, 'external_repos_cb' ),
			'cp_dir_opts'
		);

		add_settings_section(
			'cp_dir_opts_section_github_token',
			__( 'Your personal GitHub Token', 'cp-plgn-drctry' ),
			array( $this, 'github_token_cb' ),
			'cp_dir_opts'
		);

		add_settings_field(
			'cp_dir_opts_exteranal_org_repos', // As of WP 4.6 this value is used only internally.
			__( 'GitHub Organizations', 'cp-plgn-drctry' ),
			array( $this, 'external_org_repos_select_cb' ),
			'cp_dir_opts',
			'cp_dir_opts_section_external_repos',
			array(
				'label_for'               => 'cp_dir_opts_exteranal_org_repos',
				'class'                   => 'cp_dir_opts_row',
				'cp_dir_opts_custom_data' => 'custom',
			)
		);

		add_settings_field(
			'cp_dir_opts_exteranal_user_repos',
			__( 'GitHub Users', 'cp-plgn-drctry' ),
			array( $this, 'external_user_repos_select_cb' ),
			'cp_dir_opts',
			'cp_dir_opts_section_external_repos',
			array(
				'label_for'               => 'cp_dir_opts_exteranal_user_repos',
				'class'                   => 'cp_dir_opts_row',
				'cp_dir_opts_custom_data' => 'custom',
			)
		);

		add_settings_field(
			'cp_dir_opts_exteranal_repos',
			__( 'Single GitHub Repositories', 'cp-plgn-drctry' ),
			array( $this, 'external_repos_select_cb' ),
			'cp_dir_opts',
			'cp_dir_opts_section_external_repos',
			array(
				'label_for'               => 'cp_dir_opts_exteranal_repos',
				'class'                   => 'cp_dir_opts_row',
				'cp_dir_opts_custom_data' => 'custom',
			)
		);

		add_settings_field(
			'cp_dir_opts_section_github_token',
			__( 'Your personal Github Token', 'cp-plgn-drctry' ),
			array( $this, 'github_token_input_cb' ),
			'cp_dir_opts',
			'cp_dir_opts_section_github_token',
			array(
				'label_for'               => 'cp_dir_opts_section_github_token',
				'class'                   => 'cp_dir_opts_row',
				'cp_dir_opts_custom_data' => 'custom',
			)
		);

	}

	/**
	 * External Repos section callback function.
	 *
	 * @param array $args  The settings array, defining title, id, callback.
	 */
	public function external_repos_cb( $args ) {
		?>
		<div id="<?php echo esc_attr( $args['id'] ); ?>">
			<p>
			<?php esc_html_e( 'Add GitHub Organizations or Users from which you want to pull Repositories.', 'cp-plgn-drctry' ); ?>
			<br>
			<?php esc_html_e( 'The integration will automatically scan the external Repositories by the "classicpress-plugin" topic when listing Plugins, and "classicpress-theme" when listing Themes.', 'cp-plgn-drctry' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * GitHub Token section
	 *
	 * @param array $args  The settings array, defining title, id, callback.
	 */
	public function github_token_cb( $args ) {
		?>
		<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Add your personal GitHub Token to avoid API limit exhaustions.', 'cp-plgn-drctry' ); ?></p>
		<?php
	}

	/**
	 * External Orgs Repos Select Field callback function.
	 *
	 * @param array $args The array of arguments.
	 */
	public function external_org_repos_select_cb( $args ) {

		// Get the value of the setting we've registered with register_setting().
		$options  = get_option( 'cp_dir_opts_options', array( 'cp_dir_opts_exteranal_org_repos' => $this->vetted_orgs() ) );
		$_options = '';
		if ( false !== $options
			&& ! empty( $options )
			&& isset( $options[ $args['label_for'] ] )
		) {
			$orgs = $options[ $args['label_for'] ];
			foreach ( $orgs as $org ) {
				$locked    = in_array( $org, $this->vetted_orgs() ) ? 'locked="locked"' : '';
				$_options .= '<option value="' . esc_attr( $org ) . '" ' . esc_attr( $locked ) . ' selected>' . esc_html( $org ) . '</option>';
			}
		}
		?>
		<select
				id="<?php echo esc_attr( $args['label_for'] ); ?>"
				data-custom="<?php echo esc_attr( $args['cp_dir_opts_custom_data'] ); ?>"
				name="cp_dir_opts_options[<?php echo esc_attr( $args['label_for'] ); ?>][]"
				class="cp-dir-select2"
				multiple>
			<?php
			/**
			 * Reviewers: all contents of $options is prepared just a few lines above.
			 */
			echo $_options;//phpcs:ignore 
			?>
		</select>
		<p class="description">
			<?php
			/**
			 * Reviewers: the SVG Icon is passed in the constructor this class.
			 * wp_kses_post cannot deal with SVG, and insteaf of creating a silly custom validation,
			 * we can trust this output.
			 * It is never passed to translation, only the %s placeholder is.
			 */
			// translators: %s: Verified Organizations Badge.
			printf( esc_html__( 'Add GitHub Organizations by typing their exact Name, then press return/enter on your keyboard. Organizations with a %s badge cannot be removed, and have been vetted by the ClassicPress Community. Other Organizations not featuring the badge are not vetted by the community.', 'cp-plgn-drctry' ), $this->verified_badge );// phpcs:ignore
			?>
		</p>
		<?php
	}

	/**
	 * External Users Repos Select Field callback function.
	 *
	 * @param array $args The array of arguments.
	 */
	public function external_user_repos_select_cb( $args ) {

		// Get the value of the setting we've registered with register_setting().
		$options  = get_option( 'cp_dir_opts_options' );
		$_options = '';
		if ( false !== $options
			&& ! empty( $options )
			&& isset( $options[ $args['label_for'] ] )
		) {
			$orgs = $options[ $args['label_for'] ];
			foreach ( $orgs as $org ) {
				$_options .= '<option value="' . esc_attr( $org ) . '" selected>' . esc_html( $org ) . '</option>';
			}
		}
		?>
		<select
				id="<?php echo esc_attr( $args['label_for'] ); ?>"
				data-custom="<?php echo esc_attr( $args['cp_dir_opts_custom_data'] ); ?>"
				name="cp_dir_opts_options[<?php echo esc_attr( $args['label_for'] ); ?>][]"
				class="cp-dir-select2"
				multiple>
			<?php
			/**
			 * Reviewers: all contents of $options is prepared just a few lines above.
			 */
			echo $_options;//phpcs:ignore 
			?>
		</select>
		<p class="description">
			<?php esc_html_e( 'Add GitHub Users by typing their exact Name, then press return/enter on your keyboard. Caution: Users are not vetted by the community. Only Organizations can be vetted.', 'cp-plgn-drctry' ); ?>
		</p>
		<?php
	}

	/**
	 * External Single Repos Select Field callback function.
	 *
	 * @param array $args The array of arguments.
	 */
	public function external_repos_select_cb( $args ) {

		// Get the value of the setting we've registered with register_setting().
		$options  = get_option( 'cp_dir_opts_options' );
		$_options = '';
		if ( false !== $options
			&& ! empty( $options )
			&& isset( $options[ $args['label_for'] ] )
		) {
			$orgs = $options[ $args['label_for'] ];
			foreach ( $orgs as $org ) {
				$_options .= '<option value="' . esc_attr( $org ) . '" selected>' . esc_html( $org ) . '</option>';
			}
		}
		?>
		<select
				id="<?php echo esc_attr( $args['label_for'] ); ?>"
				data-custom="<?php echo esc_attr( $args['cp_dir_opts_custom_data'] ); ?>"
				name="cp_dir_opts_options[<?php echo esc_attr( $args['label_for'] ); ?>][]"
				class="cp-dir-select2"
				multiple>
			<?php
			/**
			 * Reviewers: all contents of $options is prepared just a few lines above.
			 */
			echo $_options;//phpcs:ignore 
			?>
		</select>
		<p class="description">
			<?php esc_html_e( 'Add Single GitHub Repositories by typing their exact Name in the OWNER/REPOSITORY format, then press return/enter on your keyboard.', 'cp-plgn-drctry' ); ?>
		</p>
		<?php
	}

	/**
	 * GitHub Token text field callback function.
	 *
	 * @param array $args The array of arguments.
	 */
	public function github_token_input_cb( $args ) {

		$options = get_option( 'cp_dir_opts_options' );

		?>
		<input
				type="text" 
				id="<?php echo esc_attr( $args['label_for'] ); ?>" 
				name="cp_dir_opts_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
				placeholder="Your GitHub Token"
				value="<?php echo isset( $options[ $args['label_for'] ] ) ? esc_html( $options[ $args['label_for'] ] ) : ''; ?>"
				style="width: 100%;"
		>
		<?php

	}

}
