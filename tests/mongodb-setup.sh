#!/bin/bash

# Exit on error
set -e

# Load environment variables
if [ -f .env.testing.mongodb ]; then
    # Use a temporary file to store variables
    TEMP_FILE=$(mktemp)
    
    # Clean up the environment file and handle special characters
    grep -v '^#' .env.testing.mongodb | while IFS= read -r line; do
        # Skip empty lines
        [ -z "$line" ] && continue
        
        # Properly escape special characters in values
        key=$(echo "$line" | cut -d '=' -f1)
        value=$(echo "$line" | cut -d '=' -f2-)
        
        # Remove surrounding quotes if they exist
        value=$(echo "$value" | sed 's/^"\(.*\)"$/\1/' | sed "s/^'\(.*\)'$/\1/")
        
        # Export the variable
        export "$key"="$value"
        echo "$key=$value" >> "$TEMP_FILE"
    done
    
    # Load the cleaned variables
    set -a
    source "$TEMP_FILE"
    set +a
    
    # Clean up
    rm -f "$TEMP_FILE"
fi

# Ensure MongoDB is running
if ! pgrep -x "mongod" > /dev/null; then
    echo "MongoDB is not running. Starting MongoDB..."
    if command -v brew &> /dev/null; then
        brew services start mongodb-community
    elif command -v systemctl &> /dev/null; then
        sudo systemctl start mongod
    else
        echo "Could not start MongoDB. Please start it manually and try again."
        exit 1
    fi
fi

# Create test database if it doesn't exist
mongosh --eval "db.getSiblingDB('${MONGODB_DATABASE:-gym_management_test}').createCollection('test_collection')" --quiet

# Run tests
php artisan config:clear
php artisan test --configuration=phpunit.mongodb.xml "$@"
