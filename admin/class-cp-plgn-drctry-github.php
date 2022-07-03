<?php
/**
 * Adds a trait to get Plugins from the CP API.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.2.0
 *
 * @package    Cp_Plgn_Drctry
 * @subpackage Cp_Plgn_Drctry/admin
 */

/**
 * Trait to get Plugins from the CP API.
 *
 * Sets Git Token header if available
 * Gets vetted orgs
 * Gets orgs and users repos as set in Options
 * Gets repos data
 * Gets repos readmes
 * Gets repos developers data
 * Maps data to a CP Dir Compatible object
 *
 * @package    Cp_Plgn_Drctry
 * @subpackage Cp_Plgn_Drctry/admin
 * @author     bedas <hello@tukutoi.com>
 */
trait Cp_Plgn_Drctry_GitHub {

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
	private function get_git_plugins() {

		$git_plugins = array();

		if ( ! empty( $this->options ) ) {
			if ( isset( $this->options['cp_dir_opts_exteranal_org_repos'] )
				&& ! empty( $this->options['cp_dir_opts_exteranal_org_repos'] )
			) {
				foreach ( $this->options['cp_dir_opts_exteranal_org_repos'] as $org ) {
					// $org_url = 'https://api.github.com/orgs/' . $org . '/repos';
					$org_url = 'https://api.github.com/search/repositories?q=topic:classicpress-plugin+org:' . $org;
					$git_plugins = array_merge( $git_plugins, $this->get_git_repos( $org_url, $org, 'Organization' ) );
				}
			}
			if ( isset( $this->options['cp_dir_opts_exteranal_user_repos'] )
				&& ! empty( $this->options['cp_dir_opts_exteranal_user_repos'] )
			) {
				foreach ( $this->options['cp_dir_opts_exteranal_user_repos'] as $user ) {
					// $user_url = 'https://api.github.com/users/' . $user . '/repos';
					$user_url = 'https://api.github.com/search/repositories?q=topic:classicpress-plugin+user:' . $user;
					$git_plugins = array_merge( $git_plugins, $this->get_git_repos( $user_url, $user, 'User' ) );
				}
			}
			if ( isset( $this->options['cp_dir_opts_exteranal_repos'] )
				&& ! empty( $this->options['cp_dir_opts_exteranal_repos'] )
			) {
				foreach ( $this->options['cp_dir_opts_exteranal_repos'] as $repo ) {
					$repo_url = 'https://api.github.com/repos/' . $repo;
					$git_plugins = array_merge( $git_plugins, $this->get_git_repos( $repo_url, strtok( $repo, '/' ), 'Repository' ) );
				}
			}
		}

		return $git_plugins;

	}

	/**
	 * Get all pages of the results found.
	 *
	 * @param array $response the WP Remote Get Response.
	 * @return int $last_page The last (amount of) page found.
	 */
	private function get_gh_pages( $response ) {

		$pages = wp_remote_retrieve_header( 'links', $response );
		$last_page = 0;

		if ( ! empty( $pages->link ) ) {
			$_links = explode( ',', $pages );
			$last_page = (int) rtrim( strtok( $link[1], '&page=' ), '>; rel="last"' );
		}

		return $last_page;

	}

	/**
	 * Get Plugins stored on Git.
	 *
	 * Currently only supports TukuToi Org.
	 *
	 * @param string $url    The URL to get remote response from.
	 * @param string $name   The name of the repository.
	 * @param string $domain the type of repository (org or name).
	 * @return array $git_plugins A CP API Compatible array of plugin data.
	 */
	private function get_git_repos( $url, $name, $domain ) {

		$all_git_plugins = array();
		$data = array();
		$_data = array(
			'developers' => array(),
		);
		$repos = $this->get_remote_decoded_body( $url, $this->set_auth() );

		if ( false !== $repos
			&& 404 !== $repos
		) {

			if ( 'Repository' !== $domain ) {
				$pages = $this->get_gh_pages( $repos );
				$page = 0;

				while ( $page <= $pages ) {
					$all_git_plugins = array_merge( $all_git_plugins, $this->build_git_plugins_objects( $repos, $_data ) );
					$repos = $this->get_remote_decoded_body( $url . '?page=' . $page + 1, $this->set_auth() );
					$page++;
				}
			} else {
				if ( in_array( $this->plugins_topic, $repos->topics ) ) {
					$all_git_plugins[] = $this->build_git_plugin_object( $repos, $_data );
				}
			}
		} elseif ( 404 === $repos ) {
			// Translators: %1$s: type of GitHub account (org or user), %2$s: name of account.
			echo '<div class="notice notice-error"><p>' . sprintf( esc_html__( 'We cannot find any %1$s by name "%2$s". Perhaps you made a typo when registering it the ClassicPress Repositories Settings, or the %1$s by name "%2$s" has been deleted from GitHub.', 'cp-plgn-drctry' ), esc_html( $domain ), esc_html( $name ) ) . '</p></div>';
		} else {
			// Translators: %1$s: type of GitHub account (org or user), %2$s: name of account.
			echo '<div class="notice notice-error"><p>' . sprintf( esc_html__( 'We could not fetch data for the %1$s "%2$s". It is possible you reached the limits of the GitHub Repositories API.', 'cp-plgn-drctry' ), esc_html( $domain ), esc_html( $name ) ) . '</p></div>';
		}

		return $all_git_plugins;

	}

	/**
	 * Build a CP APi compatible array of objects of repository data.
	 *
	 * @param array $repos The repositories found by the query.
	 * @param array $_data Array placeholder to cache remote Developer.
	 * @return array $git_plugins An array of repository data Objects.
	 */
	private function build_git_plugins_objects( $repos, $_data ) {

		$git_plugins = array();

		foreach ( $repos->items as $repo_object ) {

			$git_plugins[] = (object) $this->build_git_plugin_object( $repo_object, $_data );

		}

		return $git_plugins;

	}

	/**
	 * Build a CP APi compatible object of repository data.
	 *
	 * @param array $repo_object The repositories found by the query.
	 * @param array $_data       Array placeholder to cache remote Developer.
	 * @return object $git_plugins An object of Repo data.
	 */
	private function build_git_plugin_object( $repo_object, $_data ) {

		$release_data = $this->get_git_release_data( $repo_object->releases_url, $repo_object->name, $repo_object->owner->login );
		$data['name'] = $this->get_readme_name( $repo_object->name, $repo_object->owner->login, $repo_object->default_branch );
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
		$data['minimum_wp_version'] = false;
		$data['minimum_cp_version'] = false;
		$data['current_version'] = $release_data['version'];
		$data['latest_cp_compatible_version'] = false;
		$data['git_provider'] = 'GitHub';
		$data['repo_url'] = $repo_object->html_url;
		$data['download_link'] = $release_data['download_link'];
		$data['comment'] = false;
		$data['type'] = (object) array(
			'key' => 'GH',
			'value' => 0,
			'description' => 'Pulled from GitHub',
		);
		$data['published_at'] = $release_data['updated_at'];

		return (object) $data;

	}

	/**
	 * Get the "name" of the plugin - assumed to be first text following a '# ' in the readme.
	 *
	 * @since 1.3.0
	 * @param string $item   The repository slug/name.
	 * @param string $login  The repo owner name.
	 * @param string $branch The default branch of the repository.
	 * @return string $title The "name" of this plugin.
	 */
	private function get_readme_name( $item, $login, $branch ) {

		$title = esc_html__( 'No Title Found. You have to manage this Plugin manually.', 'cp-plgn-drctry' );
		$readme = $this->get_readme_data( $item, $login, $branch );
		$token = 'md' === pathinfo( $this->readme_var, PATHINFO_EXTENSION ) ? '#' : '===';

		if ( '===' === $token ) {
			$first_line = $this->get_content_between( $readme, $token, $token );
		} else {
			$first_line = $this->get_content_between( $readme, $token, "\n" );
		}

		if ( ! empty( $first_line ) ) {
			$title = sanitize_text_field( trim( $first_line[0] ) );
		}

		return $title;

	}

	/**
	 * Get readme data
	 *
	 * Readme can be:
	 * README.md
	 * readme.md
	 * README.txt
	 * readme.txt
	 *
	 * @since 1.3.0
	 * @param string $item   The repository slug/name.
	 * @param string $login  The repo owner name.
	 * @param string $branch The default branch of the repository.
	 * @return string $readme  The Readme Content.
	 */
	private function get_readme_data( $item, $login, $branch ) {

		$readme = '';
		$has_readme = false;

		foreach ( $this->readme_vars as $var ) {

			$readme = $this->get_remote_raw_body( 'https://raw.githubusercontent.com/' . $login . '/' . $item . '/' . $branch . '/' . $var );

			/**
			 * This should make sure that we have at least some form of non-error string as response.
			 * It might be literally anything though, at this point.
			 */
			if ( false !== $readme
				&& is_string( $readme )
				&& ! empty( $readme )
			) {
				$has_readme = true;
				$this->readme_var = $var;
				break;

			}
		}

		if ( false === $has_readme ) {

			// Translators: %1$s: name if repository, %2$s name of repository owner.
			echo '<div class="notice notice-error"><p>' . sprintf( esc_html__( 'We could not find a readme .md or .txt file for the Repository "%1$s". This can result in incomplete data. You should report this issue to %2$s (The Developer)', 'cp-plgn-drctry' ), esc_html( $item ), esc_html( $login ) ) . '</p></div>';

		}

		return $readme;

	}

	/**
	 * Get developer info from Github.
	 *
	 * @param string $login The Github "slug".
	 * @param string $type The Github domain type.
	 * @return array $dev_array A CP API Compatible "developer" array.
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
		$dev = $this->get_remote_decoded_body( 'https://api.github.com/' . $_type . '/' . $login, $this->set_auth() );

		if ( false !== $dev
			&& 404 !== $dev
		) {

			$dev_array = array(
				'name' => $dev->name,
				'slug' => strtolower( $dev->login ),
				'web_url' => $dev->html_url,
				'username' => '',
				'website' => $dev->blog,
				'published_at' => $dev->created_at,
			);

		} elseif ( 404 === $dev ) {

			// Translators: %1$s: type of repository, %2$s name of repository owner.
			echo '<div class="notice notice-error"><p>' . sprintf( esc_html__( 'We could not find the GitHub User/Org API for the GitHub %1$s "%2$s".', 'cp-plgn-drctry' ), esc_html( $type ), esc_html( $login ) ) . '</p></div>';

		} else {

			// Translators: %1$s: type of repository, %2$s name of repository owner.
			echo '<div class="notice notice-error"><p>' . sprintf( esc_html__( 'We could not reach the GitHub User/Org API for the GitHub %1$s "%2$s". It is possible you reached the limits of the GitHub User/Org API.', 'cp-plgn-drctry' ), esc_html( $type ), esc_html( $login ) ) . '</p></div>';

		}

		return (object) $dev_array;

	}

	/**
	 * Get Release Data from GitHub
	 *
	 * @param string $release_url The Github API Releases URL.
	 * @param string $repo_name   The repository Name.
	 * @param string $owner       The name of the repo owner.
	 * @return array $release_data A CP API Compatible "release" data array.
	 */
	private function get_git_release_data( $release_url, $repo_name, $owner ) {

		$release_data = array(
			'version' => '',
			'download_link' => '',
			'count' => 0,
			'changelog' => '',
			'updated_at' => '',
		);

		$url = str_replace( '{/id}', '/latest', $release_url );
		$release = $this->get_remote_decoded_body( $url, $this->set_auth() );

		if ( false !== $release
			&& 404 !== $release
		) {

			$release_data['version'] = $release->tag_name;
			$release_data['download_link'] = $release->assets[0]->browser_download_url;
			$release_data['count'] = $release->assets[0]->download_count;
			$release_data['changelog'] = $release->body;
			$release_data['updated_at'] = $release->assets[0]->updated_at;

		} elseif ( 404 === $release ) {

			// translators: %s: Name of remote GitHub Directory.
			echo '<div class="notice notice-error"><p>' . sprintf( esc_html__( 'It does not seem that the Repository "%1$s" by %2$s follows best practices. We could not find any Release for it on GitHub.', 'cp-plgn-drctry' ), esc_html( $repo_name ), esc_html( $owner ) ) . '</p></div>';

		} else {

			// translators: %s: Name of remote GitHub Directory.
			echo '<div class="notice notice-error"><p>' . sprintf( esc_html__( 'We could not reach the GitHub Releases API for the repository "%1$s" by %2$s. It is possible you reached the limits of the GitHub Releases API. We reccommend creating a GitHub Personal Token, then add it to the "Personal GitHub Token" setting in the Settings > Manage CP Repos menu. If you already di that, you reached 5000 hourly requests, which likely indicates that ClassicPress went viral overnight.', 'cp-plgn-drctry' ), esc_html( $repo_name ), esc_html( $owner ) ) . '</p></div>';

		}

		return $release_data;

	}

}
