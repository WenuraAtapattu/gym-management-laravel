# MongoDB Integration

This document provides information about the MongoDB integration in the Gym Management System.

## Setup

1. **Install MongoDB**
   - On macOS: `brew install mongodb-community`
   - On Ubuntu: `sudo apt-get install mongodb`
   - On Windows: Download from [MongoDB Download Center](https://www.mongodb.com/try/download/community)

2. **Install PHP MongoDB Driver**
   ```bash
   pecl install mongodb
   ```
   Add `extension=mongodb.so` (Linux/macOS) or `extension=php_mongodb.dll` (Windows) to your php.ini file.

3. **Install Laravel MongoDB Package**
   ```bash
   composer require jenssegers/mongodb
   ```

4. **Configure Environment**
   Copy `.env.testing.mongodb` to `.env.testing`:
   ```bash
   cp .env.testing.mongodb .env.testing
   ```

## Running Tests

### Using the Test Script
```bash
./tests/mongodb-setup.sh
```

### Using Artisan Command
```bash
php artisan test:mongodb
```

### Running Specific Tests
```bash
# Run a specific test file
./tests/mongodb-setup.sh tests/Feature/MongoReviewTest.php

# Run tests with a filter
./tests/mongodb-setup.sh --filter=test_user_can_create_a_review
```

## Testing with MongoDB

### Test Environment
- The test environment is configured to use a separate database (default: `gym_management_test`)
- All collections are cleared before each test
- Test data is automatically cleaned up after each test

### Writing Tests
1. Use the `MongoTestHelper` trait for common MongoDB assertions
2. Tag your MongoDB tests with `@group mongodb`
3. Use the test helpers in `tests/helpers.php` for common MongoDB operations

### Available Test Helpers
- `mongodb_collection($name)`: Get a MongoDB collection instance
- `mongodb_drop_collections($except = [])`: Drop all collections except those specified
- `mongodb_clear_collections($except = [])`: Clear all collections except those specified

## MongoDB Models

### Base Model
All MongoDB models should extend `App\Models\MongoModel`.

### Review Model
The `MongoReview` model is used for storing product reviews in MongoDB. It includes:
- Polymorphic relationship with reviewable models
- Scopes for approved/pending reviews
- Helper methods for statistics

## API Endpoints

### Reviews
- `GET /api/mongo/products/{product}/reviews` - Get reviews for a product
- `POST /api/mongo/products/{product}/reviews` - Create a new review
- `GET /api/mongo/products/{product}/reviews/{review}` - Get a specific review
- `PUT /api/mongo/products/{product}/reviews/{review}` - Update a review
- `DELETE /api/mongo/products/{product}/reviews/{review}` - Delete a review
- `GET /api/mongo/reviews/stats` - Get review statistics
- `GET /api/mongo/products/{product}/reviews/stats` - Get review statistics for a product

## Testing with PHPStorm

1. Create a new PHPUnit configuration
2. Set "Test scope" to "Defined in the configuration file"
3. Set "Configuration file" to `phpunit.mongodb.xml`
4. Set "Custom working directory" to the project root

## Troubleshooting

### MongoDB Not Running
```bash
# Start MongoDB service
brew services start mongodb-community  # macOS
sudo systemctl start mongod            # Linux
```

### PHP MongoDB Extension Not Found
```bash
# Check if the extension is installed
php -m | grep mongodb

# If not found, install it
pecl install mongodb

# Add to php.ini
echo "extension=mongodb.so" >> /path/to/php.ini
```

### Connection Refused
Make sure MongoDB is running and the connection details in `.env.testing` are correct.
