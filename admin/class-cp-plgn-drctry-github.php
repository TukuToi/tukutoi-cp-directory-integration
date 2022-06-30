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
		$this->git_org = 'tukutoi';
		$this->git_url = 'https://api.github.com/orgs/' . $this->git_org . '/repos';
		$this->tukutoi_plugin_names = array(
			'tukutoi-template-builder' => 'TukuToi Template Builder',
			'tukutoi-shortcodes' => 'TukuToi ShortCodes',
			'tukutoi-search-and-filter' => 'TukuToi Search and Filter',
			'tukutoi-cp-directory-integration' => 'CP Plugin Directory',
		);
	}

	/**
	 * Get all Git Plugins Public function.
	 */
	public function get_git_plugins() {

		return $this->build_git_plugins_object();

	}

	/**
	 * Get Plugins stored on Git.
	 *
	 * Currently only supports TukuToi Org.
	 *
	 * @return array $git_plugins A CP API Compatible array of plugin data.
	 */
	private function build_git_plugins_object() {

		$git_plugins = array();
		$data = array();
		$repos = wp_remote_get( $this->git_url );
		if ( wp_remote_retrieve_response_code( $repos ) === 200 ) {
			$repos = json_decode( wp_remote_retrieve_body( $repos ) );
		} else {
			echo '<div class="notice notice-error"><p>' . esc_html__( 'We could not reach the GitHub Repositories API. It is possible you reached the limits of the GitHub Repositories API.', 'cp-plgn-drctry' ) . '</p></div>';
			error_log( print_r( $repos, true ) );
			return $git_plugins;
		}

		foreach ( $repos as $repo_object ) {

			if ( in_array( 'classicpress-plugin', $repo_object->topics ) ) {
				$release_data = $this->get_git_release_data( $repo_object->releases_url, $repo_object->name );

				$data['name'] = $this->tukutoi_plugin_names[ $repo_object->name ];
				$data['description'] = $repo_object->description;
				$data['downloads'] = $release_data['count'];
				$data['changelog'] = $release_data['changelog'];
				$data['developer'] = (object) array(
					'name' => 'TukuToi',
					'slug' => 'tukutoi',
					'web_url' => 'https://www.tukutoi.com',
					'username' => '',
					'website' => 'https://www.tukutoi.com',
					'published_at' => '',
				);
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
		$release = wp_remote_get( $url );
		if ( wp_remote_retrieve_response_code( $release ) === 200 ) {
			$release = json_decode( wp_remote_retrieve_body( $release ) );
		} else {
			// translators: %s: Name of remote GitHub Directory.
			echo '<div class="notice notice-error"><p>' . sprintf( esc_html__( 'We could not reach the GitHub Releases API for the repository "%s". It is possible you reached the limits of the GitHub Releases API.', 'cp-plgn-drctry' ), esc_html( $repo_name ) ) . '</p></div>';
			error_log( print_r( $release, true ) );
			return $release_data;
		}

		$release_data['version'] = $release->tag_name;
		$release_data['download_link'] = $release->assets[0]->browser_download_url;
		$release_data['count'] = $release->assets[0]->download_count;
		$release_data['changelog'] = $release->body;
		$release_data['updated_at'] = $release->assets[0]->updated_at;

		return $release_data;

	}

}
