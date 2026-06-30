<?php

namespace App\Services;

use App\Models\Invoice;
use DOMDocument;
use DOMElement;

class XmlInvoiceGenerator
{
    protected Invoice $invoice;
    protected DOMDocument $dom;
    protected string $namespace = 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2';
    protected string $cac = 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2';
    protected string $cbc = 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2';

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
        $this->dom = new DOMDocument('1.0', 'UTF-8');
        $this->dom->formatOutput = true;
    }

    /**
     * Generate XML
     */
    public function generate(): string
    {
        $invoiceElement = $this->dom->createElementNS($this->namespace, 'Invoice');
        $invoiceElement->setAttribute('xmlns:cac', $this->cac);
        $invoiceElement->setAttribute('xmlns:cbc', $this->cbc);
        $invoiceElement->setAttribute('xmlns:ext', 'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2');

        $this->dom->appendChild($invoiceElement);

        // Add invoice header
        $this->addInvoiceHeader($invoiceElement);

        // Add parties
        $this->addSupplierParty($invoiceElement);
        $this->addCustomerParty($invoiceElement);

        // Add line items
        $this->addLineItems($invoiceElement);

        // Add totals
        $this->addTotals($invoiceElement);

        return $this->dom->saveXML();
    }

    /**
     * Add invoice header
     */
    protected function addInvoiceHeader(DOMElement $parent): void
    {
        $this->addElement($parent, 'cbc:UBLVersionID', '2.1');
        $this->addElement($parent, 'cbc:CustomizationID', 'urn:cefact:ubl:ph:doc:Invoice:sbas:1.0.11');
        $this->addElement($parent, 'cbc:ProfileID', 'reporting:1.0');
        $this->addElement($parent, 'cbc:ID', $this->invoice->invoice_number);
        $this->addElement($parent, 'cbc:UUID', $this->invoice->uuid);
        $this->addElement($parent, 'cbc:IssueDate', $this->invoice->invoice_date->toDateString());
        $this->addElement($parent, 'cbc:IssueTime', $this->invoice->invoice_date->toTimeString());
        $this->addElement($parent, 'cbc:DueDate', $this->invoice->due_date?->toDateString());
        $this->addElement($parent, 'cbc:InvoiceTypeCode', 'GI', [
            'listVersionID' => '1.0',
            'listID' => 'urn:cefact:ubl:cl:InvoiceTypeCode:ph:1.0.11',
            'listAgencyID' => '6',
        ]);
        $this->addElement($parent, 'cbc:DocumentCurrencyCode', $this->invoice->currency ?? 'SAR');
    }

    /**
     * Add supplier party information
     */
    protected function addSupplierParty(DOMElement $parent): void
    {
        $supplierParty = $this->addElement($parent, 'cac:AccountingSupplierParty');
        
        $party = $this->addElement($supplierParty, 'cac:Party');
        
        // Party identification
        $this->addElement($party, 'cbc:EndpointID', $this->invoice->business->tax_id, [
            'schemeID' => 'NG',
        ]);
        
        // Party name
        $partyName = $this->addElement($party, 'cac:PartyName');
        $this->addElement($partyName, 'cbc:Name', $this->invoice->business->name);
        
        // Party legal entity
        $legalEntity = $this->addElement($party, 'cac:PartyLegalEntity');
        $this->addElement($legalEntity, 'cbc:RegistrationName', $this->invoice->business->name);
        $this->addElement($legalEntity, 'cbc:CompanyID', $this->invoice->business->crn ?? '');
        
        // Party contact
        $contact = $this->addElement($party, 'cac:Contact');
        $this->addElement($contact, 'cbc:Telephone', $this->invoice->business->phone);
        $this->addElement($contact, 'cbc:ElectronicMail', $this->invoice->business->email);
        
        // Address
        $address = $this->addElement($party, 'cac:PostalAddress');
        $this->addElement($address, 'cbc:StreetName', $this->invoice->business->address);
        $this->addElement($address, 'cbc:CityName', $this->invoice->business->city);
        $this->addElement($address, 'cbc:PostalZone', $this->invoice->business->postal_code);
        
        $country = $this->addElement($address, 'cac:Country');
        $this->addElement($country, 'cbc:IdentificationCode', $this->invoice->business->country ?? 'SA');
    }

    /**
     * Add customer party information
     */
    protected function addCustomerParty(DOMElement $parent): void
    {
        $customerParty = $this->addElement($parent, 'cac:AccountingCustomerParty');
        
        $party = $this->addElement($customerParty, 'cac:Party');
        
        // Party identification
        $this->addElement($party, 'cbc:EndpointID', $this->invoice->supplier->tax_id ?? '', [
            'schemeID' => 'NG',
        ]);
        
        // Party name
        $partyName = $this->addElement($party, 'cac:PartyName');
        $this->addElement($partyName, 'cbc:Name', $this->invoice->supplier->name);
        
        // Party legal entity
        $legalEntity = $this->addElement($party, 'cac:PartyLegalEntity');
        $this->addElement($legalEntity, 'cbc:RegistrationName', $this->invoice->supplier->name);
        $this->addElement($legalEntity, 'cbc:CompanyID', $this->invoice->supplier->tax_id ?? '');
        
        // Party contact
        if ($this->invoice->supplier->phone || $this->invoice->supplier->email) {
            $contact = $this->addElement($party, 'cac:Contact');
            if ($this->invoice->supplier->phone) {
                $this->addElement($contact, 'cbc:Telephone', $this->invoice->supplier->phone);
            }
            if ($this->invoice->supplier->email) {
                $this->addElement($contact, 'cbc:ElectronicMail', $this->invoice->supplier->email);
            }
        }
        
        // Address
        if ($this->invoice->supplier->address) {
            $address = $this->addElement($party, 'cac:PostalAddress');
            $this->addElement($address, 'cbc:StreetName', $this->invoice->supplier->address);
            if ($this->invoice->supplier->city) {
                $this->addElement($address, 'cbc:CityName', $this->invoice->supplier->city);
            }
            $country = $this->addElement($address, 'cac:Country');
            $this->addElement($country, 'cbc:IdentificationCode', $this->invoice->supplier->country ?? 'SA');
        }
    }

    /**
     * Add line items
     */
    protected function addLineItems(DOMElement $parent): void
    {
        foreach ($this->invoice->items as $item) {
            $invoiceLine = $this->addElement($parent, 'cac:InvoiceLine');
            
            $this->addElement($invoiceLine, 'cbc:ID', (string)$item->id);
            $this->addElement($invoiceLine, 'cbc:InvoicedQuantity', (string)$item->quantity, [
                'unitCode' => $item->unit ?? 'C62',
            ]);
            $this->addElement($invoiceLine, 'cbc:LineExtensionAmount', number_format($item->line_subtotal, 2, '.', ''), [
                'currencyID' => $this->invoice->currency ?? 'SAR',
            ]);
            
            // Item description
            $itemElement = $this->addElement($invoiceLine, 'cac:Item');
            $this->addElement($itemElement, 'cbc:Description', $item->description);
            $this->addElement($itemElement, 'cbc:Name', $item->description);
            if ($item->sku) {
                $this->addElement($itemElement, 'cbc:SellersItemIdentification', $item->sku);
            }
            
            // Price
            $price = $this->addElement($invoiceLine, 'cac:Price');
            $this->addElement($price, 'cbc:PriceAmount', number_format($item->unit_price, 2, '.', ''), [
                'currencyID' => $this->invoice->currency ?? 'SAR',
            ]);
            
            // Tax
            if ($item->tax_amount > 0) {
                $taxTotal = $this->addElement($invoiceLine, 'cac:TaxTotal');
                $this->addElement($taxTotal, 'cbc:TaxAmount', number_format($item->tax_amount, 2, '.', ''), [
                    'currencyID' => $this->invoice->currency ?? 'SAR',
                ]);
                
                $taxSubtotal = $this->addElement($taxTotal, 'cac:TaxSubtotal');
                $this->addElement($taxSubtotal, 'cbc:TaxableAmount', number_format($item->line_subtotal, 2, '.', ''), [
                    'currencyID' => $this->invoice->currency ?? 'SAR',
                ]);
                $this->addElement($taxSubtotal, 'cbc:TaxAmount', number_format($item->tax_amount, 2, '.', ''), [
                    'currencyID' => $this->invoice->currency ?? 'SAR',
                ]);
                
                $taxCategory = $this->addElement($taxSubtotal, 'cac:TaxCategory');
                $this->addElement($taxCategory, 'cbc:ID', 'S');
                $this->addElement($taxCategory, 'cbc:Percent', number_format($item->tax_rate, 2, '.', ''));
                $this->addElement($taxCategory, 'cbc:TaxExemptionReasonCode', 'VATEX-SA-RCM');
                $this->addElement($taxCategory, 'cac:TaxScheme');
                $this->addElement($taxCategory->getElementsByTagName('TaxScheme')->item(0), 'cbc:ID', 'VAT');
            }
        }
    }

    /**
     * Add totals
     */
    protected function addTotals(DOMElement $parent): void
    {
        // Tax total
        $taxTotal = $this->addElement($parent, 'cac:TaxTotal');
        $this->addElement($taxTotal, 'cbc:TaxAmount', number_format($this->invoice->tax_amount, 2, '.', ''), [
            'currencyID' => $this->invoice->currency ?? 'SAR',
        ]);
        
        if ($this->invoice->tax_amount > 0) {
            $taxSubtotal = $this->addElement($taxTotal, 'cac:TaxSubtotal');
            $this->addElement($taxSubtotal, 'cbc:TaxableAmount', number_format($this->invoice->subtotal, 2, '.', ''), [
                'currencyID' => $this->invoice->currency ?? 'SAR',
            ]);
            $this->addElement($taxSubtotal, 'cbc:TaxAmount', number_format($this->invoice->tax_amount, 2, '.', ''), [
                'currencyID' => $this->invoice->currency ?? 'SAR',
            ]);
            
            $taxCategory = $this->addElement($taxSubtotal, 'cac:TaxCategory');
            $this->addElement($taxCategory, 'cbc:ID', 'S');
            $this->addElement($taxCategory, 'cbc:Percent', number_format(config('zatca.vat_rate') * 100, 2, '.', ''));
            
            $taxScheme = $this->addElement($taxCategory, 'cac:TaxScheme');
            $this->addElement($taxScheme, 'cbc:ID', 'VAT');
        }
        
        // Document total
        $legalMonetaryTotal = $this->addElement($parent, 'cac:LegalMonetaryTotal');
        $this->addElement($legalMonetaryTotal, 'cbc:LineExtensionAmount', number_format($this->invoice->subtotal, 2, '.', ''), [
            'currencyID' => $this->invoice->currency ?? 'SAR',
        ]);
        $this->addElement($legalMonetaryTotal, 'cbc:TaxExclusiveAmount', number_format($this->invoice->subtotal, 2, '.', ''), [
            'currencyID' => $this->invoice->currency ?? 'SAR',
        ]);
        $this->addElement($legalMonetaryTotal, 'cbc:TaxInclusiveAmount', number_format($this->invoice->total, 2, '.', ''), [
            'currencyID' => $this->invoice->currency ?? 'SAR',
        ]);
        $this->addElement($legalMonetaryTotal, 'cbc:AllowanceTotalAmount', number_format($this->invoice->discount_amount, 2, '.', ''), [
            'currencyID' => $this->invoice->currency ?? 'SAR',
        ]);
        $this->addElement($legalMonetaryTotal, 'cbc:PayableAmount', number_format($this->invoice->total, 2, '.', ''), [
            'currencyID' => $this->invoice->currency ?? 'SAR',
        ]);
    }

    /**
     * Helper to add element
     */
    protected function addElement(DOMElement $parent, string $name, string $value = '', array $attributes = []): DOMElement
    {
        $element = $this->dom->createElement($name, $value);
        
        foreach ($attributes as $key => $val) {
            $element->setAttribute($key, $val);
        }
        
        $parent->appendChild($element);
        return $element;
    }
}
