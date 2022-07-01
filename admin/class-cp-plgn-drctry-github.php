<?php
/**
 * The GitHub Integration
 *
 * @link       https://www.tukutoi.com/
 * @since      1.2.0
 *
 * @package    Cp_Plgn_Drctry
 * @subpackage Cp_Plgn_Drctry/admin
 */

/**
 * The GitHub API integration
 *
 * Loads releases from GitHub
 * Maps data to a CP Dir Compatible object
 *
 * @package    Cp_Plgn_Drctry
 * @subpackage Cp_Plgn_Drctry/admin
 * @author     bedas <hello@tukutoi.com>
 */
class Cp_Plgn_Drctry_GitHub {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The unique prefix of this plugin.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @var      string    $plugin_prefix    The string used to uniquely prefix technical functions of this plugin.
	 */
	private $plugin_prefix;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The Github Org
	 *
	 * @since    1.2.0
	 * @access   private
	 * @var      string    $git_org    GitHub Organization.
	 */
	private $git_org;
	/**
	 * The GitHub ORG URL
	 *
	 * @since    1.2.0
	 * @access   private
	 * @var      string    $git_url    GitHub URL.
	 */
	private $git_url;
	/**
	 * TukuToi Plugins seem to not follow best practices!
	 *
	 * @since    1.2.0
	 * @access   private
	 * @var      array    $tukutoi_plugin_names    A mess that beda has cooked for himself.
	 */
	private $tukutoi_plugin_names;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.2.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $plugin_prefix    The unique prefix of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $plugin_prefix, $version ) {

		$this->plugin_name   = $plugin_name;
		$this->plugin_prefix = $plugin_prefix;
		$this->version = $version;
		$this->options = get_option( 'cp_dir_opts_options', array( 'cp_dir_opts_exteranal_org_repos' => $this->vetted_orgs() ) );
	}

	/**
	 * If available, get GitHub Auth Token.
	 *
	 * @since 1.3.0
	 */
	private function set_auth() {

		$auth = array();
		if ( ! empty( $this->options ) && isset( $this->options['cp_dir_opts_section_github_token'] ) && ! empty( $this->options['cp_dir_opts_section_github_token'] ) ) {
			$auth = array(
				'headers'     => array(
					'Authorization' => 'token ' . esc_html( $this->options['cp_dir_opts_section_github_token'] ),
				),
			);
		}
		return $auth;
	}

	/**
	 * Get all Git Plugins Public function.
	 */
	public function get_git_plugins() {

		$git_plugins = array();

		if ( ! empty( $this->options ) ) {
			if ( isset( $this->options['cp_dir_opts_exteranal_org_repos'] )
				&& ! empty( $this->options['cp_dir_opts_exteranal_org_repos'] )
			) {
				foreach ( $this->options['cp_dir_opts_exteranal_org_repos'] as $org ) {
					$org_url = 'https://api.github.com/orgs/' . $org . '/repos';
					$git_plugins = array_merge( $git_plugins, $this->build_git_plugins_object( $org_url ) );
				}
			}
			if ( isset( $this->options['cp_dir_opts_exteranal_user_repos'] )
				&& ! empty( $this->options['cp_dir_opts_exteranal_user_repos'] )
			) {
				foreach ( $this->options['cp_dir_opts_exteranal_user_repos'] as $user ) {
					$user_url = 'https://api.github.com/users/' . $user . '/repos';
					$git_plugins = array_merge( $git_plugins, $this->build_git_plugins_object( $user_url ) );
				}
			}
		}

		return $git_plugins;

	}

	/**
	 * @since 1.3.0
	 */
	private function vetted_orgs() {
		$orgs = json_decode( $this->get_file_contents( '/partials/github-orgs.txt' ) );
		$_orgs = array();
		foreach ( $orgs as $org ) {
			$_orgs[] = $org->slug;
		}

		return $_orgs;
	}

	/**
	 * CP Way of getting File Contents.
	 */
	private function get_file_contents( $file ) {

		global $wp_filesystem;

		if ( ! is_a( $wp_filesystem, 'WP_Filesystem_Base' ) ) {
			include_once ABSPATH . 'wp-admin/includes/file.php';
			$creds = request_filesystem_credentials( site_url() );
			wp_filesystem( $creds );
		}

		$contents = $wp_filesystem->get_contents( __DIR__ . $file );

		return $contents;

	}
	/**
	 * Get Plugins stored on Git.
	 *
	 * Currently only supports TukuToi Org.
	 *
	 * @return array $git_plugins A CP API Compatible array of plugin data.
	 */
	private function build_git_plugins_object( $url ) {

		$git_plugins = array();
		$data = array();
		$repos = wp_remote_get( $url, $this->set_auth() );
		$_data = array(
			'developers' => array(),
		);

		if ( wp_remote_retrieve_response_code( $repos ) === 200 ) {
			$repos = json_decode( wp_remote_retrieve_body( $repos ) );
		} else {
			echo '<div class="notice notice-error"><p>' . esc_html__( 'We could not reach the GitHub Repositories API. It is possible you reached the limits of the GitHub Repositories API. We reccommend creating a GitHub Personal Token, then add it to the "Personal GitHub Token" setting in the Settings > Manage CP Repos menu. If you already di that, you reached 5000 hourly requests, which likely indicates that ClassicPress went viral overnight.', 'cp-plgn-drctry' ) . '</p></div>';
			error_log( print_r( $repos, true ) );
			return $git_plugins;
		}

		foreach ( $repos as $repo_object ) {

			if ( in_array( 'classicpress-plugin', $repo_object->topics ) ) {
				$release_data = $this->get_git_release_data( $repo_object->releases_url, $repo_object->name );

				$data['name'] = $this->get_readme_name( $repo_object->name, $repo_object->owner->login );
				$data['description'] = $repo_object->description;
				$data['downloads'] = $release_data['count'];
				$data['changelog'] = $release_data['changelog'];

				/**
				 * Avoid hitting the API again if the Developer is already stored in a previous instance.
				 */
				if ( ! array_key_exists( $repo_object->owner->login, $_data['developers'] ) ) {
					$data['developer'] = $this->get_git_dev_info( $repo_object->owner->login, $repo_object->owner->type );
				} else {
					$data['developer'] = $_data['developers'][ $repo_object->owner->login ];
				}
				$_data['developers'][ $repo_object->owner->login ] = $data['developer'];

				$data['slug'] = $repo_object->name;
				$data['web_url'] = $repo_object->html_url;
				$data['minimum_wp_version'] = '4.9.15';
				$data['minimum_cp_version'] = '1.2.0';
				$data['current_version'] = $release_data['version'];
				$data['latest_cp_compatible_version'] = '';
				$data['git_provider'] = 'GitHub';
				$data['repo_url'] = $repo_object->html_url;
				$data['download_link'] = $release_data['download_link'];
				$data['comment'] = '';
				$data['type'] = (object) array(
					'key' => 'CP',
					'value' => 0,
					'description' => 'Developed for ClassicPress',
				);
				$data['published_at'] = $release_data['updated_at'];

				$git_plugins[] = (object) $data;
			}
		}

		return $git_plugins;

	}

	/**
	 * Get Title (first line of .md or .txt, upper or lower case, after # or ==)
	 *
	 * @param string $item The repository slug/name.
	 * @param string $login The repo owner name.
	 *
	 * @since 1.3.0
	 */
	private function get_readme_name( $item, $login ) {

		$title = esc_html__( 'No Title Found. ErrNo: CP-GH-249', 'cp-plgn-drctry' );
		$first_line = strtok( $this->get_readme_data( $item, $login ), "\n" );

		if ( strpos( $this->variation, '.md' ) !== false ) {
			$title = sanitize_text_field( trim( str_replace( '#', '', $first_line ) ) );
		} elseif ( strpos( $this->variation, '.txt' ) !== false ) {
			$title = sanitize_text_field( trim( str_replace( '==', '', $first_line ) ) );
		}

		return $title;

	}

	/**
	 * Get readme data (.md or .txt, upper or lower case)
	 *
	 * @param string $item The repository slug/name.
	 * @param string $login The repo owner name.
	 *
	 * @since 1.3.0
	 */
	private function get_readme_data( $item, $login ) {

		$data = '';
		$readme_variations = array( 'README.md', 'readme.md', 'README.txt', 'readme.txt' );

		foreach ( $readme_variations as $variation ) {
			$readme = wp_remote_get( 'https://raw.githubusercontent.com/' . $login . '/' . $item . '/main/' . $variation );
			if ( wp_remote_retrieve_response_code( $readme ) !== 404 ) {
				$this->variation = $variation;
				break;
			}
		}

		if ( wp_remote_retrieve_response_code( $readme ) === 200 ) {
			$data = wp_remote_retrieve_body( $readme );
		} else {
			echo '<div class="notice notice-error"><p>' . esc_html__( 'We could not find a readme .md or .txt file for the Repository. This can result in incomplete data. You should report this issue to the Developer', 'cp-plgn-drctry' ) . '</p></div>';
			error_log( print_r( $readme, true ) );
			return $data;
		}

		return $data;

	}

	/**
	 * Get developer info from remote.
	 *
	 * @param string $login The Github "slug".
	 * @param string $type The Github domain type.
	 */
	private function get_git_dev_info( $login, $type ) {

		$dev_array = array(
			'name' => '',
			'slug' => '',
			'web_url' => '',
			'username' => '',
			'website' => '',
			'published_at' => '',
		);

		$_type = 'Organization' === $type ? 'orgs' : 'users';
		$dev = wp_remote_get( 'https://api.github.com/' . $_type . '/' . $login, $this->set_auth() );

		if ( wp_remote_retrieve_response_code( $dev ) === 200 ) {
			$dev = json_decode( wp_remote_retrieve_body( $dev ) );
		} else {
			echo '<div class="notice notice-error"><p>' . sprintf( esc_html__( 'We could not reach the GitHub User/Org API for the GitHub %$1 "%$2". It is possible you reached the limits of the GitHub User/Org API. We reccommend creating a GitHub Personal Token, then add it to the "Personal GitHub Token" setting in the Settings > Manage CP Repos menu. If you already di that, you reached 5000 hourly requests, which likely indicates that ClassicPress went viral overnight.', 'cp-plgn-drctry' ), esc_html( $type ), esc_html( $login ) ) . '</p></div>';
			error_log( print_r( $dev, true ) );
			return $dev_array;
		}

		$dev_array = array(
			'name' => $dev->name,
			'slug' => strtolower( $dev->login ),
			'web_url' => $dev->url,
			'username' => '',
			'website' => $dev->blog,
			'published_at' => $dev->created_at,
		);

		return (object) $dev_array;

	}

	/**
	 * Get Release Data from GitHub
	 *
	 * @param string $release_url  The Github API Releases URL.
	 * @return array $release_data An array with some Data from GitHub api about release.
	 */
	private function get_git_release_data( $release_url, $repo_name ) {

		$release_data = array(
			'version' => '',
			'download_link' => '',
			'count' => 0,
			'changelog' => '',
			'updated_at' => '',
		);

		$url = str_replace( '{/id}', '/latest', $release_url );
		$release = wp_remote_get( $url, $this->set_auth() );
		if ( wp_remote_retrieve_response_code( $release ) === 200 ) {
			$release = json_decode( wp_remote_retrieve_body( $release ) );
			$release_data['version'] = $release->tag_name;
			$release_data['download_link'] = $release->assets[0]->browser_download_url;
			$release_data['count'] = $release->assets[0]->download_count;
			$release_data['changelog'] = $release->body;
			$release_data['updated_at'] = $release->assets[0]->updated_at;
		} elseif ( wp_remote_retrieve_response_code( $release ) === 404 ) {
			// translators: %s: Name of remote GitHub Directory.
			echo '<div class="notice notice-error"><p>' . sprintf( esc_html__( 'It does not seem that the Repository "%s" follows best practices. We could not find any Release for it on GitHub.', 'cp-plgn-drctry' ), $repo_name ) . '</p></div>';
			error_log( print_r( $release, true ) );
		} else {
			// translators: %s: Name of remote GitHub Directory.
			echo '<div class="notice notice-error"><p>' . sprintf( esc_html__( 'We could not reach the GitHub Releases API for the repository "%s". It is possible you reached the limits of the GitHub Releases API. We reccommend creating a GitHub Personal Token, then add it to the "Personal GitHub Token" setting in the Settings > Manage CP Repos menu. If you already di that, you reached 5000 hourly requests, which likely indicates that ClassicPress went viral overnight.', 'cp-plgn-drctry' ), esc_html( $repo_name ) ) . '</p></div>';
			error_log( print_r( $release, true ) );
		}

		return $release_data;

	}

}
