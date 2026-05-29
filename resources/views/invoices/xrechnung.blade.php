<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<rsm:CrossIndustryInvoice
    xmlns:rsm="urn:un:unece:uncefact:data:standard:CrossIndustryInvoice:100"
    xmlns:qdt="urn:un:unece:uncefact:data:standard:QualifiedDataType:100"
    xmlns:ram="urn:un:unece:uncefact:data:standard:ReusableAggregateBusinessInformationEntity:100"
    xmlns:udt="urn:un:unece:uncefact:data:standard:UnqualifiedDataType:100"
    xmlns:xs="http://www.w3.org/2001/XMLSchema">

    <rsm:ExchangedDocumentContext>
        <ram:GuidelineSpecifiedDocumentContextParameter>
            <ram:ID>urn:cen.eu:en16931:2017#compliant#urn:xoev-de:kosit:standard:xrechnung_3.0</ram:ID>
        </ram:GuidelineSpecifiedDocumentContextParameter>
    </rsm:ExchangedDocumentContext>

    <rsm:ExchangedDocument>
        <ram:ID>{{ $invoice->invoice_number }}</ram:ID>
        <ram:TypeCode>380</ram:TypeCode>
        <ram:IssueDateTime>
            <udt:DateTimeString format="102">{{ $invoice->issued_at->format('Ymd') }}</udt:DateTimeString>
        </ram:IssueDateTime>
    </rsm:ExchangedDocument>

    <rsm:SupplyChainTradeTransaction>

        {{-- Line items --}}
        <ram:IncludedSupplyChainTradeLineItem>
            <ram:AssociatedDocumentLineDocument>
                <ram:LineID>1</ram:LineID>
            </ram:AssociatedDocumentLineDocument>
            <ram:SpecifiedTradeProduct>
                <ram:Name>{{ e($invoice->description) }}</ram:Name>
            </ram:SpecifiedTradeProduct>
            <ram:SpecifiedLineTradeAgreement>
                <ram:NetPriceProductTradePrice>
                    <ram:ChargeAmount>{{ number_format($invoice->net_amount, 2, '.', '') }}</ram:ChargeAmount>
                </ram:NetPriceProductTradePrice>
            </ram:SpecifiedLineTradeAgreement>
            <ram:SpecifiedLineTradeDelivery>
                <ram:BilledQuantity unitCode="C62">1</ram:BilledQuantity>
            </ram:SpecifiedLineTradeDelivery>
            <ram:SpecifiedLineTradeSettlement>
                <ram:ApplicableTradeTax>
                    <ram:TypeCode>VAT</ram:TypeCode>
                    <ram:CategoryCode>S</ram:CategoryCode>
                    <ram:RateApplicablePercent>{{ number_format($invoice->tax_rate, 2, '.', '') }}</ram:RateApplicablePercent>
                </ram:ApplicableTradeTax>
                <ram:SpecifiedTradeSettlementLineMonetarySummation>
                    <ram:LineTotalAmount>{{ number_format($invoice->net_amount, 2, '.', '') }}</ram:LineTotalAmount>
                </ram:SpecifiedTradeSettlementLineMonetarySummation>
            </ram:SpecifiedLineTradeSettlement>
        </ram:IncludedSupplyChainTradeLineItem>

        {{-- Header trade agreement --}}
        <ram:ApplicableHeaderTradeAgreement>
            <ram:BuyerReference>{{ $invoice->invoice_number }}</ram:BuyerReference>

            {{-- Seller --}}
            <ram:SellerTradeParty>
                <ram:Name>{{ e($seller['company']) }}</ram:Name>
                <ram:PostalTradeAddress>
                    <ram:PostcodeCode>{{ e($seller['zip']) }}</ram:PostcodeCode>
                    <ram:LineOne>{{ e($seller['address']) }}</ram:LineOne>
                    <ram:CityName>{{ e($seller['city']) }}</ram:CityName>
                    <ram:CountryID>{{ e($seller['country']) }}</ram:CountryID>
                </ram:PostalTradeAddress>
                @if($seller['email'])
                <ram:URIUniversalCommunication>
                    <ram:URIID schemeID="EM">{{ e($seller['email']) }}</ram:URIID>
                </ram:URIUniversalCommunication>
                @endif
                <ram:SpecifiedTaxRegistration>
                    @if($seller['vat_id'])
                    <ram:ID schemeID="VA">{{ e($seller['vat_id']) }}</ram:ID>
                    @else
                    <ram:ID schemeID="FC">{{ e($seller['tax_number']) }}</ram:ID>
                    @endif
                </ram:SpecifiedTaxRegistration>
            </ram:SellerTradeParty>

            {{-- Buyer --}}
            <ram:BuyerTradeParty>
                <ram:Name>{{ e($user->billing_company ?: $user->name) }}</ram:Name>
                <ram:PostalTradeAddress>
                    <ram:PostcodeCode>{{ e($user->billing_zip) }}</ram:PostcodeCode>
                    <ram:LineOne>{{ e($user->billing_address) }}</ram:LineOne>
                    <ram:CityName>{{ e($user->billing_city) }}</ram:CityName>
                    <ram:CountryID>{{ e($user->billing_country ?: 'DE') }}</ram:CountryID>
                </ram:PostalTradeAddress>
                <ram:URIUniversalCommunication>
                    <ram:URIID schemeID="EM">{{ e($user->email) }}</ram:URIID>
                </ram:URIUniversalCommunication>
                @if($user->billing_vat_id)
                <ram:SpecifiedTaxRegistration>
                    <ram:ID schemeID="VA">{{ e($user->billing_vat_id) }}</ram:ID>
                </ram:SpecifiedTaxRegistration>
                @endif
            </ram:BuyerTradeParty>
        </ram:ApplicableHeaderTradeAgreement>

        {{-- Delivery --}}
        <ram:ApplicableHeaderTradeDelivery>
            <ram:ActualDeliverySupplyChainEvent>
                <ram:OccurrenceDateTime>
                    <udt:DateTimeString format="102">{{ $invoice->issued_at->format('Ymd') }}</udt:DateTimeString>
                </ram:OccurrenceDateTime>
            </ram:ActualDeliverySupplyChainEvent>
        </ram:ApplicableHeaderTradeDelivery>

        {{-- Settlement --}}
        <ram:ApplicableHeaderTradeSettlement>
            <ram:InvoiceCurrencyCode>{{ $invoice->currency }}</ram:InvoiceCurrencyCode>
            @if($seller['iban'])
            <ram:SpecifiedTradeSettlementPaymentMeans>
                <ram:TypeCode>58</ram:TypeCode>
                <ram:PayeePartyCreditorFinancialAccount>
                    <ram:IBANID>{{ e($seller['iban']) }}</ram:IBANID>
                </ram:PayeePartyCreditorFinancialAccount>
                @if($seller['bic'])
                <ram:PayeeSpecifiedCreditorFinancialInstitution>
                    <ram:BICID>{{ e($seller['bic']) }}</ram:BICID>
                </ram:PayeeSpecifiedCreditorFinancialInstitution>
                @endif
            </ram:SpecifiedTradeSettlementPaymentMeans>
            @endif
            <ram:ApplicableTradeTax>
                <ram:CalculatedAmount>{{ number_format($invoice->tax_amount, 2, '.', '') }}</ram:CalculatedAmount>
                <ram:TypeCode>VAT</ram:TypeCode>
                <ram:BasisAmount>{{ number_format($invoice->net_amount, 2, '.', '') }}</ram:BasisAmount>
                <ram:CategoryCode>S</ram:CategoryCode>
                <ram:RateApplicablePercent>{{ number_format($invoice->tax_rate, 2, '.', '') }}</ram:RateApplicablePercent>
            </ram:ApplicableTradeTax>
            <ram:SpecifiedTradePaymentTerms>
                <ram:DueDateDateTime>
                    <udt:DateTimeString format="102">{{ $invoice->due_date->format('Ymd') }}</udt:DateTimeString>
                </ram:DueDateDateTime>
            </ram:SpecifiedTradePaymentTerms>
            <ram:SpecifiedTradeSettlementHeaderMonetarySummation>
                <ram:LineTotalAmount>{{ number_format($invoice->net_amount, 2, '.', '') }}</ram:LineTotalAmount>
                <ram:TaxBasisTotalAmount>{{ number_format($invoice->net_amount, 2, '.', '') }}</ram:TaxBasisTotalAmount>
                <ram:TaxTotalAmount currencyID="{{ $invoice->currency }}">{{ number_format($invoice->tax_amount, 2, '.', '') }}</ram:TaxTotalAmount>
                <ram:GrandTotalAmount>{{ number_format($invoice->gross_amount, 2, '.', '') }}</ram:GrandTotalAmount>
                <ram:DuePayableAmount>{{ number_format($invoice->gross_amount, 2, '.', '') }}</ram:DuePayableAmount>
            </ram:SpecifiedTradeSettlementHeaderMonetarySummation>
        </ram:ApplicableHeaderTradeSettlement>

    </rsm:SupplyChainTradeTransaction>
</rsm:CrossIndustryInvoice>
