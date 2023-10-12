#!/bin/bash

# Change configuration directory.
echo "Changing configuration directory..."

settings_file="/var/www/html/web/sites/default/settings.ddev.php"
old_dir="sites/default/files/sync"
new_dir="../config/sync"

sed -i -e "s|${old_dir}|${new_dir}|g" "${settings_file}"

echo "Done."
