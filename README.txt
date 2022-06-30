=== ClassicPress Plugin Directory ===
Contributors: bedas
Donate link: https://paypal.me/tukutoi
Tags: directory, plugins
Requires at least: 1.0.0
Tested up to: 4.9.15
Stable tag: 1.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds a new screen in a ClassicPress Install where you can browse, install, activate, deactivate, update, delete and paginate Plugins listed on the ClassicPress Directory *or* on GitHub (read more about this additional repository below).

== Usage ==

Install and activate like any other plugin.

Navigate to Dashboard > Plugins > Manage CP Plugins and start managing ClassicPress Plugins. 
You can install, activate, deactivate, update, delete, and also search Plugins all from within the same screen.
The Directory results are cached locally for fast performance, and you can refresh the local cache on the click of a button.

It has a pagination and a total plugins display to navigate (15 plugins a time) through the assets.
A "more info" will display all information known to ClassicPress about the plugin and developer.

The plugin requires wp_remote_get and file_put_contents to work properly on the server.

== Plugins not listed in the ClassicPress Directory ==

It is possible to manage plugins that are not listed in the ClassicPress Directory with this plugin as well.
The conditions for this to work are:
- the GitHub stored Plugin MUST have a tag `classicpress-plugin`
- the GitHub Repository MUST have a valid Release tag named witha SemVer release version (like `1.0.0`) and Public Release with a manually uploaded Release Asset in Zip Format. This ZIP MUST be uploaded to the release section for `Attach binaries by dropping them here or selecting them.`
- currently only plugins stored by the TukuToi Organization are available - in the next release, a setting will be offered to end users in order to register any organziation or user.

== Disclaimers ==
- The plugin does not take any responsibility for Plugins downloaded from the ClassicPress Directory or GitHub.
- The ClassicPress Plugin Repository is not always well maintained by the Developers who list their plugins. They forget often to bump the Version Number of their Plugins. This means, you *might* not see an update, even if there is one, or you might see an update to a certain version and get an update to a much higher version. 
- If a GitHub stored plugin is not following above (MUST) clauses, it will not be possible for this plugin to find, pull or else manage such repos.