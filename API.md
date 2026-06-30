# API Documentation

## Base URL

```
http://localhost:8000/api
```

## Authentication

Currently using no authentication. In production, implement API tokens or OAuth2.

## Response Format

All responses are JSON with the following format:

```json
{
  "success": true,
  "message": "Operation successful",
  "data": {}
}
```

## Error Responses

```json
{
  "success": false,
  "message": "Error message",
  "error": "Detailed error"
}
```

## Endpoints

### Businesses

#### List Businesses

```
GET /businesses
```

Parameters:
- `search` - Search by name or tax ID
- `status` - Filter by status (active/inactive)
- `per_page` - Items per page (default: 15)

Response:
```json
{
  "data": [
    {
      "id": 1,
      "uuid": "550e8400-e29b-41d4-a716-446655440000",
      "name": "My Company",
      "tax_id": "1234567890",
      "email": "info@company.com",
      "phone": "+966501234567",
      "is_vat_registered": true,
      "status": "active",
      "created_at": "2024-01-01T12:00:00Z"
    }
  ],
  "links": {},
  "meta": {}
}
```

#### Create Business

```
POST /businesses
```

Body:
```json
{
  "name": "My Company",
  "name_ar": "شركتي",
  "tax_id": "1234567890",
  "crn": "1010086670",
  "email": "info@company.com",
  "phone": "+966501234567",
  "address": "123 King Fahd Road",
  "city": "Riyadh",
  "postal_code": "11111",
  "country": "SA",
  "is_vat_registered": true,
  "vat_registration_date": "2023-01-01",
  "bank_iban": "SA0320000001234567890123"
}
```

#### Get Business

```
GET /businesses/{id}
```

#### Update Business

```
PUT /businesses/{id}
```

#### Delete Business

```
DELETE /businesses/{id}
```

### Suppliers

#### List Suppliers

```
GET /suppliers
```

Parameters:
- `business_id` - Filter by business
- `type` - Filter by type (customer/vendor/both)
- `search` - Search by name or tax ID
- `per_page` - Items per page

#### Create Supplier

```
POST /suppliers
```

Body:
```json
{
  "business_id": 1,
  "name": "Customer Name",
  "name_ar": "اسم العميل",
  "tax_id": "9876543210",
  "email": "customer@example.com",
  "phone": "+966501234567",
  "address": "456 Main St",
  "city": "Riyadh",
  "country": "SA",
  "type": "customer",
  "credit_limit": 50000,
  "payment_terms": "Net 30"
}
```

#### Get Supplier

```
GET /suppliers/{id}
```

#### Update Supplier

```
PUT /suppliers/{id}
```

#### Delete Supplier

```
DELETE /suppliers/{id}
```

### Invoices

#### List Invoices

```
GET /invoices
```

Parameters:
- `business_id` - Filter by business
- `status` - Filter by status (draft/finalized/submitted/reported)
- `search` - Search by invoice number
- `per_page` - Items per page

#### Create Invoice

```
POST /invoices
```

Body:
```json
{
  "business_id": 1,
  "supplier_id": 1,
  "type": "tax_invoice",
  "invoice_date": "2024-01-15 12:00:00",
  "due_date": "2024-02-15 12:00:00",
  "description": "Invoice for services rendered",
  "notes": "Payment terms: Net 30",
  "items": [
    {
      "description": "Product A",
      "description_ar": "المنتج أ",
      "sku": "SKU-001",
      "quantity": 2,
      "unit_price": 5000,
      "unit": "each",
      "tax_rate": 15
    },
    {
      "description": "Service B",
      "quantity": 1,
      "unit_price": 3000,
      "tax_rate": 15
    }
  ]
}
```

Response:
```json
{
  "success": true,
  "message": "Invoice created successfully",
  "data": {
    "id": 1,
    "uuid": "550e8400-e29b-41d4-a716-446655440001",
    "invoice_number": "INV-001001",
    "type": "tax_invoice",
    "subtotal": 13000,
    "tax_amount": 1950,
    "total": 14950,
    "status": "draft",
    "items": [
      {
        "id": 1,
        "description": "Product A",
        "quantity": 2,
        "unit_price": 5000,
        "line_total": 11500
      }
    ]
  }
}
```

#### Get Invoice

```
GET /invoices/{id}
```

#### Update Invoice

```
PUT /invoices/{id}
```

Body:
```json
{
  "description": "Updated description",
  "notes": "Updated notes",
  "due_date": "2024-02-20 12:00:00"
}
```

#### Finalize Invoice

```
POST /invoices/{id}/finalize
```

Transitions invoice from draft to finalized status. Calculates final totals and hash.

#### Get Invoice XML

```
GET /invoices/{id}/xml
```

Returns the UBL 2.1 XML representation of the invoice.

Response:
```json
{
  "success": true,
  "data": "<?xml version=\"1.0\" encoding=\"UTF-8\"?>..."
}
```

#### Get Invoice QR Code

```
GET /invoices/{id}/qrcode
```

Returns base64 encoded QR code image.

Response:
```json
{
  "success": true,
  "data": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA..."
}
```

#### Submit Invoice to ZATCA

```
POST /invoices/{id}/submit
```

Submits finalized invoice to ZATCA for approval.

Response:
```json
{
  "success": true,
  "message": "Invoice submitted to ZATCA",
  "data": {
    "uuid": "zatca-uuid-string",
    "status": "submitted"
  }
}
```

#### Delete Invoice

```
DELETE /invoices/{id}
```

Only draft invoices can be deleted.

### Tax Reports

#### Get Tax Summary

```
GET /reports/tax-summary
```

Parameters:
- `business_id` - Business ID (required)
- `period_start` - Start date (YYYY-MM-DD)
- `period_end` - End date (YYYY-MM-DD)

Response:
```json
{
  "success": true,
  "data": {
    "business_id": 1,
    "period_start": "2024-01-01",
    "period_end": "2024-01-31",
    "total_invoices": 10,
    "total_amount": 150000,
    "total_subtotal": 130434.78,
    "total_tax": 19565.22,
    "average_tax_rate": 15
  }
}
```

#### Create Tax Settlement

```
POST /reports/tax-settlement
```

Parameters:
- `business_id` - Business ID
- `period` - Period type (monthly/quarterly/annually)
- `period_start` - Start date
- `period_end` - End date

Response:
```json
{
  "success": true,
  "message": "Tax settlement created successfully",
  "data": {
    "id": 1,
    "uuid": "550e8400-e29b-41d4-a716-446655440002",
    "declaration_number": "DECL-20240115120000-1234",
    "total_invoices_amount": 150000,
    "total_tax_amount": 22500,
    "status": "draft"
  }
}
```

#### Submit Tax Declaration

```
POST /tax-declarations/{id}/submit
```

Submits tax declaration to ZATCA.

## Status Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `404` - Not Found
- `422` - Unprocessable Entity
- `500` - Server Error

## Rate Limiting

Currently not implemented. Should be added in production.

## Examples

### Complete Invoice Workflow

1. Create Business
2. Create Supplier
3. Create Invoice with items
4. Finalize Invoice (calculates totals)
5. Get XML representation
6. Get QR Code
7. Submit to ZATCA
8. Generate Tax Report
9. Create & Submit Tax Declaration

### cURL Examples

```bash
# Create business
curl -X POST http://localhost:8000/api/businesses \
  -H "Content-Type: application/json" \
  -d @business.json

# Create invoice
curl -X POST http://localhost:8000/api/invoices \
  -H "Content-Type: application/json" \
  -d @invoice.json

# Finalize invoice
curl -X POST http://localhost:8000/api/invoices/1/finalize

# Get XML
curl http://localhost:8000/api/invoices/1/xml | jq '.data'

# Submit to ZATCA
curl -X POST http://localhost:8000/api/invoices/1/submit
```
