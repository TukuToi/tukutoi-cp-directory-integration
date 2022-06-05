# ClassicPress Plugin Directory ![ClassicPress Plugin: Required CP Version](https://img.shields.io/badge/dynamic/json?color=%23057f99&label=classicpress&prefix=v&query=%24.data.minimum_cp_version&url=https%3A%2F%2Fdirectory.classicpress.net%2Fapi%2Fplugins%2Ftkt-contact-form)
[![Bugs](https://sonarcloud.io/api/project_badges/measure?project=TukuToi_tukutoi-cp-directory-integration&metric=bugs)](https://sonarcloud.io/summary/new_code?id=TukuToi_tukutoi-cp-directory-integration) [![Vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=TukuToi_tukutoi-cp-directory-integration&metric=vulnerabilities)](https://sonarcloud.io/summary/new_code?id=TukuToi_tukutoi-cp-directory-integration) [![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=TukuToi_tukutoi-cp-directory-integration&metric=sqale_rating)](https://sonarcloud.io/summary/new_code?id=TukuToi_tukutoi-cp-directory-integration) [![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=TukuToi_tukutoi-cp-directory-integration&metric=reliability_rating)](https://sonarcloud.io/summary/new_code?id=TukuToi_tukutoi-cp-directory-integration) [![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=TukuToi_tukutoi-cp-directory-integration&metric=security_rating)](https://sonarcloud.io/summary/new_code?id=TukuToi_tukutoi-cp-directory-integration)

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