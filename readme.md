# ClassicPress Plugin Directory

Enables a ClassicPress Plugin Screen to download and install ClassicPress Plugins.

## Description

Install and activate like any other plugin.
Navigate to Dashboard > Plugins > CP Plugins and start Installing Plugins. 
If the plugin installed succesfully, the button "Activate" will appear, which will lead to the "inactive" plugins screen to activate it.
An active Plugin will show a "Active" (green background) hint.
An error is shown if the plugin for some reason could not be downloaded/installed.

It has a pagination and a total plugins display to navigate (15 plugins a time) through the assets.
A "more info" will display all information known to ClassicPress about the plugin and developer.

The plugin requires wp_remote_get and file_put_contents to work properly on the server.
The plugin does not take any responsibility for Plugins downloaded from the ClassicPress Directory.

## Changelog

### 1.0.0
* [Added] Initial Release