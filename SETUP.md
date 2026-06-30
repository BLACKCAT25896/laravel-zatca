# ZATCA Setup Guide

## Prerequisites

- PHP 8.2+
- Laravel 11+
- MySQL 8.0+
- OpenSSL
- Composer

## Installation Steps

### 1. Clone Repository

```bash
git clone https://github.com/BLACKCAT25896/laravel-zatca.git
cd laravel-zatca
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_zatca
DB_USERNAME=root
DB_PASSWORD=

# ZATCA Configuration
ZATCA_MODE=development
ZATCA_USERNAME=your_username
ZATCA_PASSWORD=your_password
ZATCA_OTP=your_otp
```

### 4. Generate ZATCA Certificates

```bash
php artisan zatca:generate-certificates
```

Optional with custom DN:

```bash
php artisan zatca:generate-certificates \
  --dn-org="Your Company" \
  --dn-cn="your.domain.com"
```

### 5. Run Migrations

```bash
php artisan migrate
```

### 6. Seed Sample Data

```bash
php artisan db:seed
```

### 7. Validate Configuration

```bash
php artisan zatca:validate
```

### 8. Start Server

```bash
php artisan serve
```

Access at `http://localhost:8000`

## Docker Setup

```bash
# Build and start containers
docker-compose up -d

# Run migrations
docker-compose exec app php artisan migrate

# Seed data
docker-compose exec app php artisan db:seed

# Access shell
docker-compose exec app bash
```

Access at `http://localhost:8000`
Database admin: `http://localhost:8080`

## API Usage

### Create Business

```bash
curl -X POST http://localhost:8000/api/businesses \
  -H "Content-Type: application/json" \
  -d '{
    "name": "My Company",
    "tax_id": "1234567890",
    "email": "info@company.com",
    "phone": "+966501234567",
    "address": "123 Main St",
    "city": "Riyadh",
    "postal_code": "11111",
    "country": "SA"
  }'
```

### Create Supplier

```bash
curl -X POST http://localhost:8000/api/suppliers \
  -H "Content-Type: application/json" \
  -d '{
    "business_id": 1,
    "name": "Customer Name",
    "email": "customer@example.com",
    "type": "customer"
  }'
```

### Create Invoice

```bash
curl -X POST http://localhost:8000/api/invoices \
  -H "Content-Type: application/json" \
  -d '{
    "business_id": 1,
    "supplier_id": 1,
    "type": "tax_invoice",
    "items": [
      {
        "description": "Product Name",
        "quantity": 2,
        "unit_price": 1000,
        "tax_rate": 15
      }
    ]
  }'
```

### Finalize Invoice

```bash
curl -X POST http://localhost:8000/api/invoices/1/finalize
```

### Get Invoice XML

```bash
curl -X GET http://localhost:8000/api/invoices/1/xml
```

### Get QR Code

```bash
curl -X GET http://localhost:8000/api/invoices/1/qrcode
```

### Submit to ZATCA

```bash
curl -X POST http://localhost:8000/api/invoices/1/submit
```

### Tax Summary

```bash
curl -X GET "http://localhost:8000/api/reports/tax-summary?business_id=1&period_start=2024-01-01&period_end=2024-01-31"
```

## Testing

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/InvoiceTest.php

# Run with coverage
php artisan test --coverage
```

## ZATCA Compliance Features

✅ Phase 1 E-Invoice Generation (XML UBL 2.1)
✅ Phase 2 E-Invoice with QR Code
✅ SHA-256 Cryptographic Signing
✅ 15% VAT Calculation
✅ Invoice Hashing & Chaining
✅ Digital Signature Validation
✅ Tax Declaration & Settlement
✅ Audit Logging
✅ Certificate Management

## Project Structure

```
app/
├── Console/Commands/          # Artisan commands
├── Http/
│   ├── Controllers/           # API controllers
│   ├── Requests/              # Form requests
│   └── Resources/             # API resources
├── Models/                    # Eloquent models
├── Services/                  # Business logic
│   ├── CryptoService.php
│   ├── InvoiceService.php
│   ├── QrCodeGenerator.php
│   ├── XmlInvoiceGenerator.php
│   └── ZatcaService.php
└── Traits/                    # Reusable traits

database/
├── factories/                 # Model factories
├── migrations/                # Database migrations
└── seeders/                   # Database seeders

tests/
├── Feature/                   # Feature tests
└── Unit/                      # Unit tests
```

## ZATCA Configuration

### Environment Variables

```env
ZATCA_MODE=development              # development or production
ZATCA_ENVIRONMENT=sandbox            # sandbox or production
ZATCA_USERNAME=your_username        # ZATCA API username
ZATCA_PASSWORD=your_password        # ZATCA API password
ZATCA_OTP=your_otp                 # One-time password
ZATCA_API_URL=https://api.zatca.gov.sa/v1
ZATCA_CERTIFICATE_PATH=storage/zatca/certificate.pem
ZATCA_PRIVATE_KEY_PATH=storage/zatca/private.key
ZATCA_TIMEOUT=30
ZATCA_RETRY_ATTEMPTS=3

VAT_RATE=0.15
VAT_ENABLED=true

INVOICE_PREFIX=INV
INVOICE_SERIES_START=1000
INVOICE_QR_VERSION=2
```

## Troubleshooting

### Certificate Issues

If you get certificate errors:

```bash
# Regenerate certificates
php artisan zatca:generate-certificates --force

# Check certificate validity
php artisan zatca:validate
```

### Database Issues

```bash
# Reset database
php artisan migrate:refresh

# Reseed data
php artisan db:seed
```

### Permission Issues

```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
```

## Support & Documentation

- [ZATCA Official Documentation](https://zatca.gov.sa/)
- [UBL 2.1 Specification](http://docs.oasis-open.org/ubl/os-UBL-2.1/)
- [Laravel Documentation](https://laravel.com/docs)

## License

MIT License - see LICENSE file

## Contributing

Contributions are welcome! Please follow PSR-12 coding standards and submit pull requests.

## Security Notes

- Keep private keys secure and encrypted
- Use HTTPS for all ZATCA API communications
- Rotate certificates regularly
- Never commit `.env` or certificate files to version control
- Implement proper access controls
- Audit all invoice submissions

## Version History

### v1.0.0 (2024)
- Initial release
- Phase 1 & 2 support
- Full ZATCA compliance
- QR code generation
- Digital signatures
- Tax calculations
