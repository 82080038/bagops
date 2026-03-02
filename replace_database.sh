#!/bin/bash

# Script to replace bagops_db database with SQL file content
# Usage: ./replace_database.sh

DB_NAME="bagops_db"
DB_USER="root"
DB_PASS="root"
SQL_FILE="/var/www/html/bagops/sql/bagops_db.sql"

echo "Starting database replacement process..."

# Check if SQL file exists
if [ ! -f "$SQL_FILE" ]; then
    echo "Error: SQL file not found at $SQL_FILE"
    exit 1
fi

echo "Step 1: Dropping existing database..."
mysql -u $DB_USER -p$DB_PASS -e "DROP DATABASE IF EXISTS $DB_NAME;"

echo "Step 2: Creating new database..."
mysql -u $DB_USER -p$DB_PASS -e "CREATE DATABASE $DB_NAME;"

echo "Step 3: Importing SQL file..."
mysql -u $DB_USER -p$DB_PASS $DB_NAME < "$SQL_FILE"

echo "Step 4: Verifying database import..."
TABLE_COUNT=$(mysql -u $DB_USER -p$DB_PASS -e "USE $DB_NAME; SHOW TABLES;" | wc -l)
echo "Database imported successfully. Total tables: $((TABLE_COUNT-1))"

echo "Database replacement completed!"
