<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Cp_Plgn_Drctry
 * @subpackage Cp_Plgn_Drctry/admin/partials
 */

?>
<!-- pagination elements -->
<?php
if ( ! empty( $has_update ) ) {
	?>
	<div class="notice-warning notice">
	<?php
	foreach ( $has_update as $plugin_name => $plugin_versions ) {
		// Translators: %1$s: Plugin Name, %2$s Plugin Version.
		echo '<p>' . sprintf( esc_html__( ' %1$s should be updated to %2$s' ), esc_html( get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_name )['Name'] ), esc_html( $plugin_versions[1] ) ) . '</p>';
	}
	?>
	</div>
	<?php
}
?>
<div class="tablenav top">
	<div class="alignleft actions">
		<a class="button no-margin" href="<?php echo esc_url( wp_nonce_url( 'plugins.php?page=cp-plugins&refresh=1', 'tkt-refresh-data', 'tkt_nonce' ) ); ?>"><?php esc_html_e( 'Refresh List', 'cp-plgn-drctry' ); ?></a>
	</div>
	<div class="aligncenter form actions">
		<?php $this->search_form(); ?>
		<a class="button no-margin" href="<?php echo esc_url( get_admin_url( null, 'plugins.php?page=cp-plugins', 'admin' ) ); ?>"><?php esc_html_e( 'Reset', 'cp-plgn-drctry' ); ?></a>
	</div>
	<h2 class="screen-reader-text"><?php esc_html_e( 'Plugins list navigation', 'cp-plgn-drctry' ); ?></h2>
	<div class="tablenav-pages">
		<span class="displaying-num"><?php printf( '%s %s', (int) count( $plugins ), esc_html__( 'Plugins found', 'cp-plgn-drctry' ) ); ?>.</span>
		<span class="pagination-links">
			<a class="first-page" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'paged', 0 ), 'tkt_page_nonce', 'tkt_page_nonce' ) ); ?>"><span class="screen-reader-text"><?php esc_html_e( 'First page', 'cp-plgn-drctry' ); ?></span><span aria-hidden="true">«</span></a>
			<a class="prev-page" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'paged', (int) $prev ), 'tkt_page_nonce', 'tkt_page_nonce' ) ); ?>"><span class="screen-reader-text"><?php esc_html_e( 'Previous page', 'cp-plgn-drctry' ); ?></span><span aria-hidden="true">‹</span></a>
			<span class="current-page"><?php echo (int) $paged; ?></span> of <span class="total-pages"><?php echo (int) $last; ?></span>
			<a class="next-page" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'paged', (int) $next ), 'tkt_page_nonce', 'tkt_page_nonce' ) ); ?>"><span class="screen-reader-text"><?php esc_html_e( 'Next page', 'cp-plgn-drctry' ); ?></span><span aria-hidden="true">›</span></a>
			<a class="last-page" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'paged', (int) $last ), 'tkt_page_nonce', 'tkt_page_nonce' ) ); ?>"><span class="screen-reader-text"><?php esc_html_e( 'Last page', 'cp-plgn-drctry' ); ?></span><span aria-hidden="true">»</span></a>
		</span>
	</div>
</div>
<!-- list elements -->
<div class="wp-list-table widefat plugin-install">
	<div class="the-list">
		
		<?php
		foreach ( $current_plugins as $single_plugin ) {

			$is_installed = $this->plugin_fx->check_plugin_installed( $single_plugin );
			$is_active = $this->plugin_fx->check_plugin_active( $single_plugin );
			$plugin_slug = $this->plugin_fx->plugin_slug( $single_plugin );
			/**
			 * Not all plugin developers have a forum profile.
			 */
			$contact_link = ! empty( $single_plugin->developer->username ) ? 'https://forums.classicpress.net/u/' . rawurlencode( esc_html( $single_plugin->developer->username ) ) : $single_plugin->developer->website;

			?>
			<div class="plugin-card plugin-card-<?php echo esc_html( $single_plugin->slug ); ?>">
				<div class="plugin-card-top">
					<div class="name column-name" style="margin-left: unset;">
						<h3>
							<a href="#TB_inline?&width=600&height=550&inlineId=<?php echo esc_html( $single_plugin->slug ); ?>" class="thickbox"><?php echo esc_html( $single_plugin->name ); ?></a>
						</h3>
					</div>
					<div class="action-links" style="margin-left: unset;">
						<ul class="plugin-action-buttons">
							<li>
								<?php
								if ( false === $is_active && false === $is_installed ) {
									?>
								<a class="install-now plugin-action button" id="button-<?php echo esc_html( $single_plugin->slug ); ?>" data-action="install" data-slug="<?php echo esc_html( $single_plugin->slug ); ?>" href="<?php echo esc_url( $single_plugin->download_link ); ?>" aria-label="Install <?php echo esc_html( $single_plugin->name ); ?> now" data-name="<?php echo esc_html( $single_plugin->name ); ?>"><?php esc_html_e( 'Install Now', 'cp-plgn-drctry' ); ?></a>
									<?php
								} elseif ( false === $is_active && ! array_key_exists( $plugin_slug, $has_update ) ) {
									?>
									<a class="activate-now plugin-action button" data-action="activate" id="button-<?php echo esc_html( $single_plugin->slug ); ?>" data-slug="<?php echo esc_html( $plugin_slug ); ?>" href="" aria-label="Activate <?php echo esc_html( $single_plugin->name ); ?> now" data-name="<?php echo esc_html( $single_plugin->name ); ?>"><?php esc_html_e( 'Activate Now', 'cp-plgn-drctry' ); ?></a>
									<?php
								} elseif ( false === $is_active && array_key_exists( $plugin_slug, $has_update )
									|| true === $is_active && array_key_exists( $plugin_slug, $has_update )
								) {
									?>
									<a class="update-now plugin-action button" data-action="update" id="button-<?php echo esc_html( $single_plugin->slug ); ?>" data-slug="<?php echo esc_html( $plugin_slug ); ?>" href="<?php echo esc_url( $single_plugin->download_link ); ?>" aria-label="Update <?php echo esc_html( $single_plugin->name ); ?> now" data-name="<?php echo esc_html( $single_plugin->name ); ?>"><?php esc_html_e( 'Update Now', 'cp-plgn-drctry' ); ?></a>
									<small>
										<?php
										// Translators: %1$s: old version number, %2$s: new version number.
										printf( esc_html__( 'From v%1$s to v%2$s', 'cp-plgn-drctry' ), esc_html( $has_update[ $plugin_slug ][0] ), esc_html( $single_plugin->current_version ) );
										?>
									</small>
									<?php
								} else {
									?>
									<a class="deactivate-now plugin-action button" data-action="deactivate" id="button-<?php echo esc_html( $single_plugin->slug ); ?>" data-slug="<?php echo esc_html( $plugin_slug ); ?>" href="" aria-label="Update <?php echo esc_html( $single_plugin->name ); ?> now" data-name="<?php echo esc_html( $single_plugin->name ); ?>"><?php esc_html_e( 'Deactivate Now', 'cp-plgn-drctry' ); ?></a>
								<?php } ?>

							</li>
							<?php
							if ( false === $is_active && true === $is_installed ) {

								?>
								<li>
									<a class="delete-now plugin-action button" data-action="delete" id="button-<?php echo esc_html( $single_plugin->slug ); ?>" data-slug="<?php echo esc_html( $plugin_slug ); ?>" href="" aria-label="Delete <?php echo esc_html( $single_plugin->name ); ?> now" data-name="<?php echo esc_html( $single_plugin->name ); ?>"><?php esc_html_e( 'Delete', 'cp-plgn-drctry' ); ?></a>
								</li>
								<?php
							}
							?>
							<li>
								<a href="#TB_inline?&width=600&height=550&inlineId=<?php echo esc_html( $single_plugin->slug ); ?>" class="thickbox"><?php esc_html_e( 'More Details', 'cp-plgn-drctry' ); ?></a>
							</li>
						</ul>
					</div>
					<div class="desc column-description" style="margin-left: unset;">
						<p><?php echo esc_html( substr( $single_plugin->description, 0, 40 ) . '...' ); ?></p>
						<p class="authors"> 
							<cite>By <a href="<?php echo esc_url_raw( $single_plugin->developer->web_url ); ?>"><?php echo esc_html( $single_plugin->developer->name ); ?></a></cite>
						</p>
						<p>
							<small><a href="<?php echo esc_url_raw( $contact_link ); ?>"><?php esc_html_e( 'Contact the Developer', 'cp-plgn-drctry' ); ?></a></small> | 
							<small><a style="color:rgba(255, 0, 0, 0.51);" href="mailto:plugins@classicpress.net?subject=Plugin Report for <?php echo esc_html( $single_plugin->name ); ?>&body=Please review this plugin: <?php echo esc_html( $single_plugin->name ); ?>. It has the following problems which require the ClassicPress Plugin Review Team action: {LIST THE ISSUES HERE. PLEASE ONLY USE THIS IF YOU THINK THE PLUGIN MUST BE IMMEDIATELY SUSPENDED. OTHERWISE CONTACT THE AUTHOR: https://forums.classicpress.net/u/<?php echo esc_html( $single_plugin->developer->username ); ?>}."><?php esc_html_e( 'Report this Plugin', 'cp-plgn-drctry' ); ?></a></small>
						</p>
					</div>
				</div>
				<div id="<?php echo esc_html( $single_plugin->slug ); ?>" style="display:none;">
					<div class="more-info-modal">
						<a href="<?php echo esc_url( $single_plugin->repo_url ); ?>#readme" class="button"><?php esc_html_e( 'Read More on GitHub', 'cp-plgn-drctry' ); ?></a>
						<?php
						$allowed_html = array(
							'p' => array(),
							'li' => array(),
							'h2' => array(),
						);
						echo wp_kses( $this->more_info( $single_plugin ), 'post' );
						?>
					</div>
				</div>
			</div>

			<?php
		}
		?>
	</div>
</div> 
<div class="tablenav bottom">
	<div class="tablenav-pages">
		<span class="displaying-num"><?php printf( '%s %s', (int) count( $plugins ), esc_html__( 'Plugins found', 'cp-plgn-drctry' ) ); ?>.</span>
		<span class="pagination-links">
			<a class="first-page" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'paged', 0 ), 'tkt_page_nonce', 'tkt_page_nonce' ) ); ?>"><span class="screen-reader-text"><?php esc_html_e( 'First page', 'cp-plgn-drctry' ); ?></span><span aria-hidden="true">«</span></a>
			<a class="prev-page" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'paged', (int) $prev ), 'tkt_page_nonce', 'tkt_page_nonce' ) ); ?>"><span class="screen-reader-text"><?php esc_html_e( 'Previous page', 'cp-plgn-drctry' ); ?></span><span aria-hidden="true">‹</span></a>
			<span class="current-page"><?php echo (int) $paged; ?></span> of <span class="total-pages"><?php echo (int) $last; ?></span>
			<a class="next-page" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'paged', (int) $next ), 'tkt_page_nonce', 'tkt_page_nonce' ) ); ?>"><span class="screen-reader-text"><?php esc_html_e( 'Next page', 'cp-plgn-drctry' ); ?></span><span aria-hidden="true">›</span></a>
			<a class="last-page" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'paged', (int) $last ), 'tkt_page_nonce', 'tkt_page_nonce' ) ); ?>"><span class="screen-reader-text"><?php esc_html_e( 'Last page', 'cp-plgn-drctry' ); ?></span><span aria-hidden="true">»</span></a>
		</span>
	</div>
</div>
