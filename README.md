# Laravel ZATCA E-Invoice Implementation

A comprehensive Laravel project implementing full ZATCA (Zakat, Tax and Customs Authority) compliance for KSA businesses, including Phase 1 and Phase 2 e-invoicing with cryptographic signing and QR code generation.

## Features

- ✅ Phase 1 E-Invoice Generation (XML format)
- ✅ Phase 2 E-Invoice with QR Code
- ✅ ZATCA Cryptographic Signing (SHA-256)
- ✅ QR Code Generation and Validation
- ✅ Invoice Management System
- ✅ Tax Calculation and Settlement
- ✅ Business and Supplier Management
- ✅ Comprehensive API Endpoints
- ✅ Unit & Integration Tests
- ✅ Docker Support

## Stack

- **Language:** PHP 8.2+
- **Framework:** Laravel 11
- **Database:** MySQL/MariaDB
- **Queue:** Redis
- **Notable Libraries:**
  - `ramsey/uuid` - UUID generation
  - `illuminate/encryption` - Cryptographic operations
  - `chillerlan/php-qrcode` - QR code generation
  - `spatie/laravel-data` - Data validation
  - `intervention/image` - Image processing

## Installation

### Prerequisites

- PHP 8.2+
- Composer
- MySQL 8.0+
- Redis (optional, for queues)
- OpenSSL for certificate generation

### Quick Start

```bash
# Clone the repository
git clone https://github.com/BLACKCAT25896/laravel-zatca.git
cd laravel-zatca

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Generate ZATCA certificates
php artisan zatca:generate-certificates

# Run migrations
php artisan migrate

# Seed sample data
php artisan db:seed

# Start development server
php artisan serve
```

Visit `http://localhost:8000`

## API Endpoints

### Invoices
- `POST /api/invoices` - Create invoice
- `GET /api/invoices/{id}` - Get invoice details
- `GET /api/invoices/{id}/xml` - Download XML
- `GET /api/invoices/{id}/qrcode` - Get QR code
- `POST /api/invoices/{id}/submit` - Submit to ZATCA

### Businesses
- `GET /api/businesses` - List businesses
- `POST /api/businesses` - Create business
- `PUT /api/businesses/{id}` - Update business

### Suppliers
- `GET /api/suppliers` - List suppliers
- `POST /api/suppliers` - Create supplier

### Tax Reports
- `GET /api/reports/tax-summary` - Tax summary
- `POST /api/reports/settlement` - Submit tax settlement

## Configuration

Update `.env` with your ZATCA credentials:

```env
ZATCA_MODE=development  # development or production
ZATCA_OTP=<your-otp>
ZATCA_USERNAME=<your-username>
ZATCA_PASSWORD=<your-password>
ZATCA_CERTIFICATE_PATH=storage/zatca/certificate.pem
ZATCA_PRIVATE_KEY_PATH=storage/zatca/private.key
```

## Project Structure

```
laravel-zatca/
├── app/
│   ├── Models/              # Eloquent models (Invoice, Business, etc.)
│   ├── Services/            # Business logic (ZATCA, Invoice services)
│   ├── Http/
│   │   ├── Controllers/     # API controllers
│   │   ├── Requests/        # Form request validation
│   │   └── Resources/       # API resource classes
│   ├── Jobs/                # Queue jobs
│   ├── Events/              # Application events
│   ├── Listeners/           # Event listeners
│   ├── Commands/            # Artisan commands
│   └── Traits/              # Reusable traits
├── database/
│   ├── migrations/          # Database migrations
│   ├── seeders/             # Database seeders
│   └── factories/           # Model factories
├── routes/
│   ├── api.php              # API routes
│   └── web.php              # Web routes
├── tests/
│   ├── Unit/                # Unit tests
│   ├── Feature/             # Feature tests
│   └── Fixtures/            # Test data
├── storage/
│   └── zatca/               # ZATCA certificates & keys
├── config/
│   └── zatca.php            # ZATCA configuration
├── docker-compose.yml       # Docker setup
└── Dockerfile               # Application container
```

## Database Schema

- `businesses` - Business information
- `suppliers` - Supplier/customer information
- `invoices` - Invoice records
- `invoice_items` - Invoice line items
- `invoice_signatures` - Digital signatures
- `tax_declarations` - Tax settlement records
- `audit_logs` - Compliance audit trails

## Testing

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/InvoiceTest.php

# Run with coverage
php artisan test --coverage
```

## Docker

```bash
# Build and start containers
docker-compose up -d

# Run migrations in container
docker-compose exec app php artisan migrate

# Access container shell
docker-compose exec app bash
```

## ZATCA Compliance

This implementation follows ZATCA technical specifications:

- SHA-256 cryptographic hashing
- XML invoice format (UBL 2.1)
- QR code encoding with invoice data
- Timestamp validation
- Certificate-based authentication
- Proper tax calculation (15% VAT)

## Security

- All sensitive data encrypted at rest
- TLS for all API communications
- Rate limiting on endpoints
- CSRF protection
- Input validation and sanitization
- Certificate pinning support

## License

MIT License - see LICENSE file

## Support

For issues and questions, please open a GitHub issue.

## Contributing

Contributions are welcome! Please follow PSR-12 coding standards.
