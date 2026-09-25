<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quote {{ $quote->quote_number }} — EMAC Development</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Instrument Sans', sans-serif; }
        body { background: #f8fafc; color: #0f172a; padding: 30px; font-size: 13px; line-height: 1.5; }
        .invoice-box { max-width: 800px; margin: auto; background: #fff; padding: 40px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 24px; border-bottom: 2px solid #C5A059; margin-bottom: 24px; }
        .brand-title { font-size: 22px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px; }
        .brand-subtitle { font-size: 11px; color: #C5A059; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        .quote-title { font-size: 20px; font-weight: 800; font-family: monospace; color: #0f172a; text-align: right; }
        .quote-status { display: inline-block; padding: 3px 10px; border-radius: 9999px; font-size: 10px; font-weight: 700; text-transform: uppercase; margin-top: 4px; background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px; }
        .info-card { background: #f8fafc; padding: 16px; border-radius: 12px; border: 1px solid #e2e8f0; }
        .info-title { font-size: 10px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; margin-bottom: 6px; }
        .info-value { font-size: 13px; font-weight: 700; color: #0f172a; }
        .info-sub { font-size: 12px; color: #475569; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin: 24px 0; }
        th { background: #f1f5f9; padding: 10px 14px; font-size: 11px; text-transform: uppercase; font-weight: 700; color: #475569; text-align: left; border-bottom: 1px solid #cbd5e1; }
        td { padding: 12px 14px; border-bottom: 1px solid #f1f5f9; font-size: 12px; }
        .text-right { text-align: right; }
        .total-row { background: #faf5eb; font-weight: 800; font-size: 15px; color: #8F6B20; }
        .terms-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; margin-top: 24px; font-size: 11px; color: #475569; }
        .no-print { margin-bottom: 20px; display: flex; justify-content: flex-end; gap: 10px; }
        .btn { padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; border: none; }
        .btn-primary { background: #0f172a; color: #fff; }
        .btn-gold { background: #C5A059; color: #0f172a; }
        @media print {
            body { padding: 0; background: #fff; }
            .invoice-box { border: none; box-shadow: none; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="btn btn-primary" onclick="window.close()">Close</button>
        <button class="btn btn-gold" onclick="window.print()">Print Quotation</button>
    </div>

    <div class="invoice-box">
        <div class="header">
            <div>
                <div class="brand-title">EMAC DEVELOPMENT</div>
                <div class="brand-subtitle">Commercial & Residential Property Services</div>
                <div style="font-size: 11px; color: #64748b; margin-top: 4px;">Grand Cayman · Cayman Islands</div>
            </div>
            <div style="text-align: right;">
                <div class="quote-title">{{ $quote->quote_number }}</div>
                <div class="quote-status">{{ $quote->status->label() }}</div>
                <div style="font-size: 11px; color: #64748b; margin-top: 6px;">Date: <strong>{{ $quote->created_at->format('M d, Y') }}</strong></div>
                <div style="font-size: 11px; color: #64748b;">Expires: <strong>{{ $quote->expires_at ? $quote->expires_at->format('M d, Y') : 'N/A' }}</strong></div>
            </div>
        </div>

        <div class="grid-2">
            <div class="info-card">
                <div class="info-title">Customer / Client Information</div>
                <div class="info-value">{{ $quote->user?->name ?? 'Valued Customer' }}</div>
                <div class="info-sub">{{ $quote->user?->email }}</div>
                <div class="info-sub">{{ $quote->user?->phone ?? 'Phone not provided' }}</div>
            </div>
            <div class="info-card">
                <div class="info-title">Service Location & Reference</div>
                <div class="info-value">#REQ-{{ str_pad($quote->service_request_id, 5, '0', STR_PAD_LEFT) }}</div>
                <div class="info-sub">{{ $quote->serviceRequest?->address?->address ?? 'Location not specified' }}</div>
                <div class="info-sub">{{ $quote->serviceRequest?->property_information ?? 'Residential Property' }}</div>
            </div>
        </div>

        <div class="info-card" style="margin-bottom: 20px;">
            <div class="info-title">Scope of Work & Service Description</div>
            <div style="font-size: 12px; color: #1e293b; white-space: pre-line; margin-top: 4px;">
                {{ $quote->service_description }}
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">#</th>
                    <th>Cost Item / Deliverable</th>
                    <th class="text-right" style="width: 150px;">Amount ($)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="color: #94a3b8;">1</td>
                    <td><strong>Labor & Technical Work</strong></td>
                    <td class="text-right font-mono">${{ number_format($quote->labor_cost, 2) }}</td>
                </tr>
                <tr>
                    <td style="color: #94a3b8;">2</td>
                    <td><strong>Materials & Hardware Supplies</strong></td>
                    <td class="text-right font-mono">${{ number_format($quote->materials_cost, 2) }}</td>
                </tr>
                <tr>
                    <td style="color: #94a3b8;">3</td>
                    <td><strong>Equipment & Machinery Usage</strong></td>
                    <td class="text-right font-mono">${{ number_format($quote->equipment_cost, 2) }}</td>
                </tr>
                <tr>
                    <td style="color: #94a3b8;">4</td>
                    <td><strong>Trip Charge / Service Call Fee</strong></td>
                    <td class="text-right font-mono">${{ number_format($quote->trip_charge, 2) }}</td>
                </tr>
                <tr>
                    <td style="color: #94a3b8;">5</td>
                    <td><strong>Additional Logistics & Permitting</strong></td>
                    <td class="text-right font-mono">${{ number_format($quote->additional_charges, 2) }}</td>
                </tr>
                <tr style="border-top: 2px solid #cbd5e1;">
                    <td colspan="2" class="text-right" style="font-weight: 700; color: #475569;">Subtotal:</td>
                    <td class="text-right font-mono" style="font-weight: 700;">${{ number_format($quote->subtotal, 2) }}</td>
                </tr>
                @if ($quote->discount > 0)
                    <tr>
                        <td colspan="2" class="text-right" style="font-weight: 700; color: #059669;">Promotional Discount:</td>
                        <td class="text-right font-mono" style="font-weight: 700; color: #059669;">-${{ number_format($quote->discount, 2) }}</td>
                    </tr>
                @endif
                @if ($quote->tax_amount > 0 || $quote->tax_rate > 0)
                    <tr>
                        <td colspan="2" class="text-right" style="font-weight: 700; color: #475569;">Tax ({{ number_format($quote->tax_rate, 1) }}%):</td>
                        <td class="text-right font-mono" style="font-weight: 700;">+${{ number_format($quote->tax_amount, 2) }}</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td colspan="2" class="text-right" style="text-transform: uppercase; letter-spacing: 0.5px;">Grand Total Quotation:</td>
                    <td class="text-right font-mono" style="font-size: 16px;">${{ number_format($quote->total_price, 2) }}</td>
                </tr>
            </tbody>
        </table>

        @if ($quote->terms_and_conditions)
            <div class="terms-box">
                <div style="font-weight: 700; text-transform: uppercase; color: #334155; margin-bottom: 4px;">Terms & Conditions:</div>
                <div style="white-space: pre-line;">{{ $quote->terms_and_conditions }}</div>
            </div>
        @endif

        <div style="margin-top: 30px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 15px;">
            Thank you for choosing EMAC Development · For inquiries, contact support@emac.test
        </div>
    </div>

</body>
</html>
