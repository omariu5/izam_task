# Inventory Management System - Izam Task

A Laravel-based REST API for managing warehouse inventory, stock transfers, and inventory tracking.

## Setup Instructions

1. Clone the repository:
```bash
git clone https://github.com/omariu5/izam_task
cd izam_task
```

2. Install PHP dependencies:
```bash
composer install
```

3. Set up environment file:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure database and other settings in `.env`

## SQLite Configuration

1. Create SQLite database file:
```bash
touch database/database.sqlite
```

2. Update `.env` configuration:
```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

Note: Replace `/absolute/path/to/database.sqlite` with the actual path to your database file.

## Running Migrations and Seeders

1. Run migrations to create database tables:
```bash
php artisan migrate
```

2. Run seeders to populate initial data:
```bash
php artisan db:seed
```

This will create:
- 5 sample warehouses
- 20 inventory items
- Random stock distributions
- Default admin user

## Authentication with Sanctum

### Default User Credentials
```
Email: admin@example.com
Password: password
```

### Obtaining an Authentication Token

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password",
    "device_name": "postman"
  }'
```

The response will include your access token:
```json
{
    "token": "your-access-token",
    "user": {
        "id": 1,
        "name": "Test Admin",
        "email": "admin@example.com"
    }
}
```

### Using the Token

Include the token in subsequent requests using the Bearer authentication header:
```bash
Authorization: Bearer your-access-token
```

## Importing Postman Collection

1. Open Postman
2. Click "Import" button
3. Select the `Inventory Management.postman_collection.json` file from the project root
4. Create an environment and set:
   - `base_url`: Your API base URL (e.g., `http://localhost:8000`)
   - After login, set `access_token` with the received token

## API Examples

### Get Inventory List
```bash
curl -X GET 'http://localhost:8000/api/inventory?name=test&min_price=10&max_price=100&warehouse_id=1' \
  -H 'Authorization: Bearer your-access-token'
```

Available filters:
- `name`: Search by item name
- `sku`: Filter by SKU
- `min_price`: Minimum price
- `max_price`: Maximum price
- `warehouse_id`: Filter by warehouse

### Transfer Stock
```bash
curl -X POST http://localhost:8000/api/stock-transfers \
  -H 'Authorization: Bearer your-access-token' \
  -H "Content-Type: application/json" \
  -d '{
    "inventory_item_id": 1,
    "from_warehouse_id": 1,
    "to_warehouse_id": 2,
    "quantity": 5
  }'
```

### View Warehouse Inventory
```bash
curl -X GET http://localhost:8000/api/warehouses/1/inventory \
  -H 'Authorization: Bearer your-access-token'
```

## Troubleshooting

### SQLite Permission Issues

1. **Database file not writable**
   ```bash
   # Set correct permissions
   chmod 666 database/database.sqlite
   chmod 777 database
   ```

2. **Database directory not writable**
   ```bash
   # Create directory with proper permissions
   mkdir -p database
   chmod 777 database
   ```


### Common Issues

1. **Token Mismatch**
   - Ensure you're including the correct token
   - Token might be expired - obtain a new one
   - Check if you're using the full token string

2. **Database Connection Failed**
   - Verify SQLite path is absolute in `.env`
   - Check file permissions
   - Ensure SQLite PHP extension is enabled

3. **404 Not Found**
   - Ensure you're using the correct API endpoints
   - Check if the Laravel server is running
   - Verify the base URL in your requests

4. **Low Stock Threshold**
   - Default threshold is 10 units
   - Can be modified in `.env`:
   ```env
   LOW_STOCK_THRESHOLD=10
   ```

5. **Email Notifications**
   - Configure SMTP in `.env`:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=your-smtp-host
   MAIL_PORT=587
   MAIL_USERNAME=your-username
   MAIL_PASSWORD=your-password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=your-email
   ```
   - Start queue worker:
   ```bash
   php artisan queue:work
   ```
   - If SMTP is not configured or queue worker is not running:
     - Emails will be logged to `storage/logs/laravel.log`
     - You can still track notification attempts in the logs

## Additional Notes

- The API uses Laravel Sanctum for authentication
- All responses are paginated (15 items per page)
- Stock transfers are handled in transactions to ensure data integrity
- Low stock alerts are triggered when stock falls below threshold
- All dates are in UTC

## License

[MIT License](https://opensource.org/licenses/MIT)

This project is licensed under the MIT License - see the above link for details.
