#!/bin/bash
unset DB_CONNECTION
unset DB_DATABASE
mkdir -p database && touch database/database.sqlite
php artisan config:clear
php artisan serve --host=127.0.0.1 --port=8080
