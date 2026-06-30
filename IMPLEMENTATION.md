# ZATCA Invoice Management System - Comprehensive Implementation

## 🎯 Overview

This is a complete Laravel-based implementation of ZATCA (Zakat, Tax and Customs Authority) compliance for KSA businesses, supporting both Phase 1 and Phase 2 e-invoicing with advanced features.

## ✨ Key Features

### Phase 1 & 2 Support
- ✅ UBL 2.1 XML Invoice Generation
- ✅ Digital QR Code Generation
- ✅ SHA-256 Cryptographic Signing
- ✅ Invoice Hashing & Chaining
- ✅ 15% VAT Calculation
- ✅ Tax Settlement Reports

### Business Management
- 🏢 Multiple Business Support
- 👥 Customer/Vendor Management
- 📋 Credit Limits & Balance Tracking
- 🏦 Bank Account Integration
- 📍 Multi-location Support

### Invoice Management
- 📄 Full Invoice Lifecycle
- 📦 Line Item Management
- 💰 Dynamic Pricing & Discounts
- 📊 Tax Calculation
- 🔐 Digital Signatures
- 📤 ZATCA Submission

### Reporting & Compliance
- 📈 Tax Summaries
- 📊 Tax Settlements
- 🔍 Audit Logs
- 📋 Declaration Management
- 📤 ZATCA Reporting

### Security
- 🔒 Certificate Management
- 🔑 RSA Encryption
- 🛡️ Security Headers
- 📝 Comprehensive Audit Trail
- 🚫 Input Validation & Sanitization

## 📋 Requirements

- PHP 8.2+
- Laravel 11
- MySQL 8.0+
- Redis (optional)
- OpenSSL
- Composer

## 🚀 Quick Start

### Docker (Recommended)

```bash
# Clone repository
git clone https://github.com/BLACKCAT25896/laravel-zatca.git
cd laravel-zatca

# Start containers
docker-compose up -d

# Run setup
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan zatca:generate-certificates
```

Access at `http://localhost:8000`

### Traditional Setup

```bash
# Clone repository
git clone https://github.com/BLACKCAT25896/laravel-zatca.git
cd laravel-zatca

# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Generate certificates
php artisan zatca:generate-certificates

# Database setup
php artisan migrate
php artisan db:seed

# Start server
php artisan serve
```

## 📚 Documentation

- [SETUP.md](SETUP.md) - Complete setup guide
- [API.md](API.md) - API endpoint documentation
- [CONTRIBUTING.md](CONTRIBUTING.md) - Contributing guidelines

## 🔌 API Endpoints

### Businesses
```
GET    /api/businesses
POST   /api/businesses
GET    /api/businesses/{id}
PUT    /api/businesses/{id}
DELETE /api/businesses/{id}
```

### Invoices
```
GET    /api/invoices
POST   /api/invoices
GET    /api/invoices/{id}
PUT    /api/invoices/{id}
DELETE /api/invoices/{id}
POST   /api/invoices/{id}/finalize
POST   /api/invoices/{id}/submit
GET    /api/invoices/{id}/xml
GET    /api/invoices/{id}/qrcode
```

### Tax Reports
```
GET    /api/reports/tax-summary
POST   /api/reports/tax-settlement
POST   /api/tax-declarations/{id}/submit
GET    /api/tax-declarations/{id}
```

## 🗄️ Database Schema

- **businesses** - Business/company information
- **suppliers** - Customers/vendors
- **invoices** - Invoice records
- **invoice_items** - Line items
- **invoice_signatures** - Digital signatures
- **tax_declarations** - Tax settlements
- **audit_logs** - Compliance audit trails

## ⚙️ Configuration

### Environment Variables

```env
# App
APP_NAME="Laravel ZATCA"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_zatca
DB_USERNAME=root
DB_PASSWORD=

# ZATCA
ZATCA_MODE=development
ZATCA_ENVIRONMENT=sandbox
ZATCA_USERNAME=your_username
ZATCA_PASSWORD=your_password
ZATCA_OTP=your_otp
ZATCA_API_URL=https://api.zatca.gov.sa/v1

# Certificates
ZATCA_CERTIFICATE_PATH=storage/zatca/certificate.pem
ZATCA_PRIVATE_KEY_PATH=storage/zatca/private.key

# VAT
VAT_RATE=0.15
VAT_ENABLED=true
```

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test tests/Feature/InvoiceTest.php

# Run with coverage
php artisan test --coverage
```

## 📦 Project Structure

```
laravel-zatca/
├── app/
│   ├── Console/Commands/        # Artisan commands
│   ├── Events/                  # Application events
│   ├── Exceptions/              # Exception handlers
│   ├── Http/
│   │   ├── Controllers/         # API controllers
│   │   ├── Middleware/          # HTTP middleware
│   │   ├── Requests/            # Form requests
│   │   └── Resources/           # API resources
│   ├── Jobs/                    # Queue jobs
│   ├── Listeners/               # Event listeners
│   ├── Models/                  # Eloquent models
│   ├── Providers/               # Service providers
│   ├── Services/                # Business logic
│   │   ├── CryptoService.php
│   │   ├── InvoiceService.php
│   │   ├── QrCodeGenerator.php
│   │   ├── XmlInvoiceGenerator.php
│   │   └── ZatcaService.php
│   └── Traits/                  # Reusable traits
├── bootstrap/                   # Laravel bootstrap
├── config/                      # Configuration files
├── database/
│   ├── factories/               # Model factories
│   ├── migrations/              # Database migrations
│   └── seeders/                 # Database seeders
├── routes/
│   ├── api.php                  # API routes
│   └── web.php                  # Web routes
├── storage/
│   ├── app/                     # Application storage
│   ├── logs/                    # Application logs
│   └── zatca/                   # ZATCA certificates
├── tests/
│   ├── Feature/                 # Feature tests
│   └── Unit/                    # Unit tests
├── docker-compose.yml           # Docker composition
├── Dockerfile                   # Docker image
├── artisan                      # Artisan command
├── composer.json                # Composer dependencies
├── .env.example                 # Environment template
└── README.md                    # This file
```

## 🔐 Security Considerations

1. **Certificate Management**
   - Certificates stored in `storage/zatca/`
   - Never commit certificates to version control
   - Regular certificate rotation recommended
   - Private keys protected with 0600 permissions

2. **API Security**
   - HTTPS enforced in production
   - Rate limiting on endpoints
   - Input validation & sanitization
   - CSRF protection
   - Security headers configured

3. **Data Protection**
   - All sensitive data encrypted at rest
   - Audit logs for compliance
   - Database encryption recommended
   - Secure password hashing

4. **Access Control**
   - API authentication (add middleware)
   - Role-based access control (RBAC)
   - Business isolation
   - User permissions tracking

## 📞 Support

- GitHub Issues: [Report bugs](https://github.com/BLACKCAT25896/laravel-zatca/issues)
- Documentation: [SETUP.md](SETUP.md)
- API Docs: [API.md](API.md)

## 🤝 Contributing

Contributions welcome! See [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

```bash
# Fork repository
# Create feature branch: git checkout -b feature/name
# Commit changes: git commit -am 'Add feature'
# Push to branch: git push origin feature/name
# Submit pull request
```

## 📄 License

MIT License - See [LICENSE](LICENSE) file

## 🙏 Acknowledgments

- ZATCA for technical specifications
- Laravel community
- OASIS UBL Working Group

## 🎓 Learning Resources

- [ZATCA E-Invoice System](https://zatca.gov.sa/)
- [UBL 2.1 Standards](http://docs.oasis-open.org/ubl/os-UBL-2.1/)
- [Laravel Documentation](https://laravel.com/docs)
- [PHP OpenSSL Functions](https://www.php.net/manual/en/book.openssl.php)

---

**Made with ❤️ for KSA businesses**
