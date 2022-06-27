# ClassicPress Plugin Directory ![ClassicPress Plugin: Required CP Version](https://img.shields.io/badge/dynamic/json?color=%23057f99&label=classicpress&prefix=v&query=%24.data.minimum_cp_version&url=https%3A%2F%2Fdirectory.classicpress.net%2Fapi%2Fplugins%2Ftkt-contact-form)
[![Bugs](https://sonarcloud.io/api/project_badges/measure?project=TukuToi_tukutoi-cp-directory-integration&metric=bugs)](https://sonarcloud.io/summary/new_code?id=TukuToi_tukutoi-cp-directory-integration) [![Vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=TukuToi_tukutoi-cp-directory-integration&metric=vulnerabilities)](https://sonarcloud.io/summary/new_code?id=TukuToi_tukutoi-cp-directory-integration) [![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=TukuToi_tukutoi-cp-directory-integration&metric=sqale_rating)](https://sonarcloud.io/summary/new_code?id=TukuToi_tukutoi-cp-directory-integration) [![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=TukuToi_tukutoi-cp-directory-integration&metric=reliability_rating)](https://sonarcloud.io/summary/new_code?id=TukuToi_tukutoi-cp-directory-integration) [![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=TukuToi_tukutoi-cp-directory-integration&metric=security_rating)](https://sonarcloud.io/summary/new_code?id=TukuToi_tukutoi-cp-directory-integration)

[![slack](https://img.shields.io/badge/Community%20%26%20Support-grey?style=for-the-badge&logo=slack&logoColor=white&label=slack&labelColor=4A154B)](https://tukutoi.slack.com/join/shared_invite/zt-1b1x1844z-_~~4pikNzssevxwnx3BqCA#/shared-invite/email)

Enables a ClassicPress Plugin Screen to browse, install, activate, deactivate, update, delete and paginate ClassicPress Plugins.

## Description

Install and activate like any other plugin.

Navigate to Dashboard > Plugins > CP Plugins and start Installing Plugins. 
You can install, activate, deactivate, update, delete, and also search Plugins from within the same screen.
The Directory results are cached locally for fast performance, and you can refresh the local cache on the click of a button.

It has a pagination and a total plugins display to navigate (15 plugins a time) through the assets.
A "more info" will display all information known to ClassicPress about the plugin and developer.

The plugin requires wp_remote_get and file_put_contents to work properly on the server.
The plugin does not take any responsibility for Plugins downloaded from the ClassicPress Directory.

## Changelog

### 1.2.0
[Added] Total Page Number on pagination
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