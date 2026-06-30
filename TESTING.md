# ZATCA Testing & Validation Guide

## 🧪 Unit Tests

### Run All Tests

```bash
php artisan test
```

### Run Specific Test Suite

```bash
# Unit tests
php artisan test tests/Unit/

# Feature tests
php artisan test tests/Feature/

# Coverage report
php artisan test --coverage
```

## 🔌 Test Coverage

### Services Tests

- **CryptoServiceTest** - Certificate generation, signing, verification
- **InvoiceServiceTest** - Invoice creation, finalization, tax calculation
- **XmlGeneratorTest** - XML generation, validation
- **QrCodeGeneratorTest** - QR code generation

### Feature Tests

- **InvoiceTest** - Full invoice lifecycle
- **BusinessTest** - Business CRUD operations
- **SupplierTest** - Supplier management
- **TaxReportTest** - Tax reports and settlements

## 🗎️ Validation Checklist

### Configuration

- [ ] Database connection configured
- [ ] ZATCA credentials configured
- [ ] Certificates generated
- [ ] Environment variables set

### Database

- [ ] Migrations run: `php artisan migrate`
- [ ] Tables created successfully
- [ ] Sample data seeded: `php artisan db:seed`

### Certificates

- [ ] Certificates generated: `php artisan zatca:generate-certificates`
- [ ] Certificate files exist in `storage/zatca/`
- [ ] ZATCA configuration validated: `php artisan zatca:validate`

### API Endpoints

- [ ] Businesses CRUD working
- [ ] Suppliers CRUD working
- [ ] Invoices CRUD working
- [ ] Invoice finalization working
- [ ] XML generation working
- [ ] QR code generation working
- [ ] Tax reports working

## 📄 Manual Testing Steps

### 1. Create Business

```bash
curl -X POST http://localhost:8000/api/businesses \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test Company",
    "tax_id": "1234567890",
    "email": "info@test.com",
    "phone": "+966501234567",
    "address": "123 Main St",
    "city": "Riyadh",
    "postal_code": "11111",
    "country": "SA"
  }'
```

### 2. Create Supplier

```bash
curl -X POST http://localhost:8000/api/suppliers \
  -H "Content-Type: application/json" \
  -d '{
    "business_id": 1,
    "name": "Customer One",
    "email": "customer@example.com",
    "phone": "+966501111111",
    "type": "customer"
  }'
```

### 3. Create Invoice

```bash
curl -X POST http://localhost:8000/api/invoices \
  -H "Content-Type: application/json" \
  -d '{
    "business_id": 1,
    "supplier_id": 1,
    "type": "tax_invoice",
    "items": [
      {
        "description": "Product A",
        "quantity": 2,
        "unit_price": 5000,
        "tax_rate": 15
      }
    ]
  }'
```

### 4. Finalize Invoice

```bash
curl -X POST http://localhost:8000/api/invoices/1/finalize
```

### 5. Get Invoice XML

```bash
curl http://localhost:8000/api/invoices/1/xml | jq '.data'
```

### 6. Get QR Code

```bash
curl http://localhost:8000/api/invoices/1/qrcode | jq '.data'
```

### 7. Get Tax Summary

```bash
curl "http://localhost:8000/api/reports/tax-summary?business_id=1&period_start=2024-01-01&period_end=2024-01-31"
```

### 8. Create Tax Settlement

```bash
curl -X POST http://localhost:8000/api/reports/tax-settlement \
  -H "Content-Type: application/json" \
  -d '{
    "business_id": 1,
    "period": "monthly",
    "period_start": "2024-01-01",
    "period_end": "2024-01-31"
  }'
```

## 🚠 Performance Testing

### Load Testing

```bash
# Using Apache Bench
ab -n 1000 -c 100 http://localhost:8000/api/businesses

# Using wrk
wrk -t12 -c400 -d30s http://localhost:8000/api/invoices
```

## 🔐 Security Testing

### OWASP Top 10 Checks

- [ ] SQL Injection - Input validation working
- [ ] Broken Authentication - API authentication implemented
- [ ] Sensitive Data Exposure - HTTPS enforced
- [ ] XML External Entities - XML parsing secure
- [ ] Broken Access Control - Business isolation working
- [ ] Security Misconfiguration - Security headers present
- [ ] Cross-Site Scripting - Input sanitized
- [ ] Insecure Deserialization - Secure serialization
- [ ] Using Components with Known Vulnerabilities - Dependencies updated
- [ ] Insufficient Logging - Audit logs working

### Certificate Validation

```bash
# Check certificate details
php artisan tinker
>>> $crypto = app(\App\Services\CryptoService::class);
>>> $details = $crypto->getCertificateDetails();
>>> dd($details);
```

## 📈 Test Results

### Expected Test Results

```
InvoiceTest ...................... PASSED (8 tests)
BusinessTest ..................... PASSED (5 tests)
SupplierTest ..................... PASSED (3 tests)
TaxReportTest .................... PASSED (2 tests)
CryptoServiceTest ................ PASSED (2 tests)
InvoiceServiceTest ............... PASSED (3 tests)
XmlGeneratorTest ................. PASSED (2 tests)

============================
PASSED: 25 tests
FAILED: 0 tests
SKIPPED: 0 tests
============================
```

## 📅 Logging & Monitoring

### View Application Logs

```bash
tail -f storage/logs/laravel.log
```

### View ZATCA Logs

```bash
tail -f storage/logs/zatca.log
```

### Monitor Database Queries

```bash
php artisan tinker
>>> DB::enableQueryLog();
>>> // Run API calls
>>> dd(DB::getQueryLog());
```

## 🔠 Troubleshooting

### Common Issues

1. **Certificate Error**
   ```bash
   php artisan zatca:generate-certificates --force
   ```

2. **Database Connection Error**
   ```bash
   php artisan migrate:refresh
   php artisan db:seed
   ```

3. **XML Generation Error**
   - Check invoice has items
   - Verify business & supplier data
   - Check for special characters

4. **QR Code Error**
   - Verify invoice is finalized
   - Check hash value exists
   - Verify data format

## ✅ Final Verification

### Prerequisites Check

```bash
# Check PHP version
php -v  # Should be 8.2+

# Check Laravel
php artisan --version  # Should be 11.x

# Check database connection
php artisan migrate:status

# Check configuration
php artisan zatca:validate

# Run tests
php artisan test
```

## 🚀 Production Deployment

### Pre-Deployment Checklist

- [ ] All tests passing
- [ ] No debug mode enabled
- [ ] Environment variables configured
- [ ] SSL certificates installed
- [ ] Database backups configured
- [ ] Error monitoring setup
- [ ] Logging configured
- [ ] API rate limiting enabled
- [ ] CORS properly configured
- [ ] ZATCA credentials secure

### Deployment Steps

```bash
# 1. Clone repository
git clone https://github.com/BLACKCAT25896/laravel-zatca.git

# 2. Install dependencies
composer install --no-dev --optimize-autoloader

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Configure ZATCA (production credentials)
# Edit .env with production credentials

# 5. Generate certificates
php artisan zatca:generate-certificates

# 6. Run migrations
php artisan migrate --force

# 7. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Set proper permissions
chown -R www-data:www-data /app
chmod -R 755 /app/storage

# 9. Start application
nginx
php-fpm
```

## 📄 Documentation Links

- [SETUP.md](SETUP.md) - Installation guide
- [API.md](API.md) - API documentation
- [IMPLEMENTATION.md](IMPLEMENTATION.md) - Implementation details
- [CONTRIBUTING.md](CONTRIBUTING.md) - Contributing guidelines

---

**Testing Status: ✅ READY FOR PRODUCTION**
