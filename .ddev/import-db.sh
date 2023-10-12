#!/bin/bash

# Change configuration directory.
echo "Checking database..."

# Use a table that should exist in the database.
if ! mysql -e 'SELECT * FROM users;' db 2>/dev/null; then
  echo 'Importing sample database...'
  # Provide path to custom database.
  gzip -dc /var/www/html/.ddev/assets/db/db.sql.gz | mysql db
  echo "Done."
else
  echo "Database ready."
fi
