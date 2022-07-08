# ClassicPress Directory Integration

[![Bugs](https://sonarcloud.io/api/project_badges/measure?project=TukuToi_tukutoi-cp-directory-integration&metric=bugs)](https://sonarcloud.io/summary/new_code?id=TukuToi_tukutoi-cp-directory-integration) [![Vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=TukuToi_tukutoi-cp-directory-integration&metric=vulnerabilities)](https://sonarcloud.io/summary/new_code?id=TukuToi_tukutoi-cp-directory-integration) [![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=TukuToi_tukutoi-cp-directory-integration&metric=sqale_rating)](https://sonarcloud.io/summary/new_code?id=TukuToi_tukutoi-cp-directory-integration) [![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=TukuToi_tukutoi-cp-directory-integration&metric=reliability_rating)](https://sonarcloud.io/summary/new_code?id=TukuToi_tukutoi-cp-directory-integration) [![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=TukuToi_tukutoi-cp-directory-integration&metric=security_rating)](https://sonarcloud.io/summary/new_code?id=TukuToi_tukutoi-cp-directory-integration)

[![slack](https://img.shields.io/badge/Community%20%26%20Support-grey?style=for-the-badge&logo=slack&logoColor=white&label=slack&labelColor=4A154B)](https://tukutoi.slack.com/join/shared_invite/zt-1b1x1844z-_~~4pikNzssevxwnx3BqCA#/shared-invite/email)

Adds a new screen in a ClassicPress Install where you can browse, install, activate, deactivate, update, delete and paginate Plugins listed on the ClassicPress Directory *or* on GitHub (read more about this additional repository below).

## Usage

Install and activate like any other plugin.

Navigate to Dashboard > Plugins > Manage CP Plugins and start managing ClassicPress Plugins. 
You can install, activate, deactivate, update, delete, and also search Plugins all from within the same screen.
The results are cached locally for fast performance, and you can refresh the local cache on the click of a button.

It has a pagination and a total plugins display to navigate (15 plugins a time) through the assets.
A "more info" will display all information known to ClassicPress about the plugin and developer.

The plugin requires wp_remote_get and file_put_contents to work properly on the server.

## Plugins not listed in the ClassicPress Directory

It is possible to manage plugins that are not listed in the ClassicPress Directory with this plugin as well.
The conditions for this to work are:
- the GitHub stored Plugin MUST have a tag `classicpress-plugin`.
- the GitHub Repository MUST have a valid Release tag named with a SemVer release version (like `1.0.0`) .
- the release MUST have a manually uploaded Zip Asset uploaded to the release section for `Attach binaries by dropping them here or selecting them.` holding the plugin.
- the repository MUST have EITHER OR BOTH a readme.txt OR readme.md (can be all uppercase too). The readme.txt is prioritized and MUST follow the WordPress readme.txt rules. The readme.md file is used only as backup, and if used, MUST have at least one line featuring `# Plugin Name Here`.
- the repository MUST be public.

By default, there is a _vetted list_ of _Organizations_ added to the plugin. If a Developer wants to appear on said list,
they can submit a PR to the `github-orgs.txt` File of this Plugin, by adding their Guthub Organization data to the JSON array.
The Organization AND the PR initiator will be reviewed both by the author of this plugin as well the ClassicPress Plugin Review Team.
Only after careful assessment the Developer will be added to the Verified List of Organizations, and thus appear pre-selected in the Repositories queried by this plugin.

Other, non verified Repositories (both users and orgs) can still be added easily by an end user in the dedicated Settings page (Dashboard > Settings > Manage CP Repos).

## Disclaimers
- The plugin does not take any responsibility for Plugins downloaded from the ClassicPress Directory or GitHub, not even if verified Organization's software.
- The ClassicPress Plugin Repository is not always well maintained by the Developers who list their plugins. They forget often to bump the Version Number of their Plugins. This means, you *might* not see an update, even if there is one, or you might see an update to a certain version and get an update to a much higher version. 
- If a GitHub stored plugin is not following above (MUST) clauses, it will not be possible for this plugin to find, pull or else manage such repos.
- If you run into GitHub API Limits (it is not so generous) you should create a Personal Authentication Token as shown [here](https://docs.github.com/en/enterprise-server@3.4/authentication/keeping-your-account-and-data-secure/creating-a-personal-access-token). You should only give this Token "read" rights, no post or edit rights. You should _never_ share this Token with anyone. You should then store this Token on the setting for it under Dashboard > Settings > Manage CP Repos. This will bump your GitHub API limits to 5000 per hours (which is far enough).

## Changelog

### 1.3.0
[Added] Plugin Settings Page (under Admin > Settings > Manage CP Repos)
[Added] Setting to add custom GitHub Repositories of Orgs, Users or single Repos.
[Added] Setting to store Personal GitHub Token, which increases the API Limits to 5k hourly instead of 60.
[Added] Verified Orgs (_not users_) are pre-selected. A PR can be used to add new Orgs to the vetted list.
[Added] Fundations to read remote readme, README, (both in md or txt) files. Currently used ony for below [Fixed] item.
[Fixed] Problem where plugins with foldername/distinct-filename.php AND a unguessable Plugin Title could not be managed.
[Improved] Make drastically less calls to the GitHub API by re-using already queried data as much as possible.
[Improved] Refactored Plugin Code.

### 1.2.0
[Added] GitHub Repo Sync for (TukuToi) Plugins
[Added] Total Page Number on pagination
[Added] Improved Error notices
[Changed] Moved the "report this plugin" to the left in the cards

### 1.1.4
[Fixed] Plugins (unless the integration itself) got deactivated after Updating.

### 1.1.3
[Fixed] Plugin could not update itself.
[Fixed] Request-URI Too Long when performing several searches without resetting the search.
[Fixed] Unused third argument in AJAX operations removed.

### 1.1.0 
[Added] AJAXified install/activate/deactivate/update/delete buttons/features. 
[Added] Bottom pagination
[Fixed] Design of main search bar

### 1.0.1
[Fixed] Added fallback for when no mail client is installed on user computer.

### 1.0.0
* [Added] Initial Release