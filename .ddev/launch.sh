#!/bin/bash

# Login and launch.
echo "Logging in and launching..."

site_uri=$(ddev exec "drush st --format=list --fields uri")
uri_length=${#site_uri}
((uri_length++))
login_url=$(ddev exec "drush uli /")
login_path=$(echo "${login_url}" | cut -c ${uri_length}-)
echo ${login_path}
ddev launch "${login_path}"

echo "Done."
