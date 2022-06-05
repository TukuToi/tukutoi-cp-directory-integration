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
<div class="tablenav top">
	<div class="alignleft actions">
		<a class="button no-margin" href="<?php echo esc_url( wp_nonce_url( 'plugins.php?page=cp-plugins&refresh=1', 'tkt-refresh-data', 'tkt_nonce' ) ); ?>"><?php esc_html_e( 'Refresh List', 'cp-plgn-drctry' ); ?></a>
		<?php $this->search_form(); ?>
		<a class="button no-margin" href="<?php echo esc_url( get_admin_url( null, 'plugins.php?page=cp-plugins', 'admin' ) ); ?>"><?php esc_html_e( 'Reset', 'cp-plgn-drctry' ); ?></a>
	</div>
	<h2 class="screen-reader-text"><?php esc_html_e( 'Plugins list navigation', 'cp-plgn-drctry' ); ?></h2>
	<div class="tablenav-pages">
		<span class="displaying-num"><?php printf( '%s %s', (int) count( $plugins ), esc_html__( 'Plugins found', 'cp-plgn-drctry' ) ); ?>.</span>
		<span class="pagination-links">
			<a class="first-page" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'paged', 0 ), 'tkt_page_nonce', 'tkt_page_nonce' ) ); ?>"><span class="screen-reader-text"><?php esc_html_e( 'First page', 'cp-plgn-drctry' ); ?></span><span aria-hidden="true">«</span></a>
			<a class="prev-page" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'paged', (int) $prev ), 'tkt_page_nonce', 'tkt_page_nonce' ) ); ?>"><span class="screen-reader-text"><?php esc_html_e( 'Previous page', 'cp-plgn-drctry' ); ?></span><span aria-hidden="true">‹</span></a>
			<span class="tkt-current-page"><?php echo (int) $paged; ?></span>
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

			$is_installed = $this->check_plugin_installed( $single_plugin );
			$is_active = $this->check_plugin_active( $single_plugin );
			$plugin_slug = $this->plugin_slug( $single_plugin );

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
								<a class="install-now button" id="button-<?php echo esc_html( $single_plugin->slug ); ?>" data-slug="<?php echo esc_html( $single_plugin->slug ); ?>" href="<?php echo esc_url( $single_plugin->download_link ); ?>" aria-label="Install <?php echo esc_html( $single_plugin->name ); ?> now" data-name="<?php echo esc_html( $single_plugin->name ); ?>">Install Now</a>
									<?php
								} elseif ( false === $is_active ) {
									printf(
										'<a class="activate-now button" href="%s" target="_parent">%s</a>',
										esc_url( wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . rawurlencode( $plugin_slug ), 'activate-plugin_' . $plugin_slug ) ),
										esc_html__( 'Activate', 'cp-plgn-drctry' )
									);
									?>
									
									<?php
								} else {
									?>
									<span class="active-cp-plugin" aria-label="This Plugin Is Active" >Active</span>
								<?php } ?>

							</li>
							<li>
								<a href="#TB_inline?&width=600&height=550&inlineId=<?php echo esc_html( $single_plugin->slug ); ?>" class="thickbox">More Details</a>
							</li>
							<li>
								<small><a style="color:rgba(255, 0, 0, 0.31);" href="mailto:plugins@classicpress.net?subject=Plugin Report for <?php echo esc_html( $single_plugin->name ); ?>&body=Please review this plugin: <?php echo esc_html( $single_plugin->name ); ?>. It has the following problems which require the ClassicPress Plugin Review Team action: {LIST THE ISSUES HERE. PLEASE ONLY USE THIS IF YOU THINK THE PLUGIN MUST BE IMMEDIATELY SUSPENDED. OTHERWISE CONTACT THE AUTHOR: https://forums.classicpress.net/u/<?php echo esc_html( $single_plugin->developer->username ); ?>}."><?php esc_html_e( 'Report this Plugin', 'cp-plgn-drctry' ); ?></a></small>
							</li>
						</ul>
					</div>
					<div class="desc column-description" style="margin-left: unset;">
						<p><?php echo esc_html( substr( $single_plugin->description, 0, 40 ) . '...' ); ?></p>
						<p class="authors"> 
							<cite>By <a href="<?php echo esc_url_raw( $single_plugin->developer->web_url ); ?>"><?php echo esc_html( $single_plugin->developer->name ); ?></a></cite>
						</p>
						<small><a href="https://forums.classicpress.net/u/<?php echo rawurlencode( esc_html( $single_plugin->developer->username ) ); ?>"><?php esc_html_e( 'Contact the Developer', 'cp-plgn-drctry' ); ?></a></small>
					</div>
				</div>
				<div id="<?php echo esc_html( $single_plugin->slug ); ?>" style="display:none;">
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

			<?php
		}
		?>
	</div>
</div> 
