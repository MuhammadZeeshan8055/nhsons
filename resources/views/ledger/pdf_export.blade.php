<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Ledger Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 18px;
            color: #666;
            margin-bottom: 10px;
            margin-top: -40px;
        }
        .customer-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }
        .customer-info h3 {
            margin-top: 0;
            color: #333;
            font-size: 16px;
        }
        .info-row {
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            color: #555;
            width: 120px;
            display: inline-block;
        }
        .table-container {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #333;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary-table {
            width: 50%;
            margin-left: auto;
            margin-top: 20px;
        }
        .summary-table th,
        .summary-table td {
            padding: 10px;
            font-size: 13px;
        }
        .summary-table th {
            background-color: #e9ecef;
        }
        .total-due {
            background-color: #fff3cd;
            font-weight: bold;
        }
        .total-due.negative {
            background-color: #f8d7da;
            color: #721c24;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }
        .page-break {
            page-break-after: always;
        }
        .balance-positive {
            color: #dc3545;
        }
        .balance-negative {
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="header">
         <div class="company-info">
            <img src="https://nhsons.com/assets/img/nh_logo.png" alt="NHSONS Logo" widht="350px" height="250px" class="company-logo">
            <!--<div class="company-name">NHSONS</div>-->
        </div>
        <div class="report-title">Customer Ledger Report</div>
        <div style="font-size: 12px; color: #666;">Generated on: {{ date('d F, Y', strtotime($generatedDate)) }}</div>
    </div>

    <div class="customer-info">
        <h3>Customer Information</h3>
        <div class="info-row">
            <span class="info-label">Customer Name:</span>
            <span>{{ $customer->nama }}</span>
        </div>
        @if($customer->email)
        <div class="info-row">
            <span class="info-label">Email:</span>
            <span>{{ $customer->email }}</span>
        </div>
        @endif
        @if($customer->telepon)
        <div class="info-row">
            <span class="info-label">Phone:</span>
            <span>{{ $customer->telepon }}</span>
        </div>
        @endif
        @if($customer->alamat)
        <div class="info-row">
            <span class="info-label">Address:</span>
            <span>{{ $customer->alamat }}</span>
        </div>
        @endif
    </div>

    <div class="table-container">
        <h3>Transaction History</h3>
        
        @if($ledgerEntries->count() > 0)
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 8%;">S.No</th>
                    <th class="text-center" style="width: 15%;">Date</th>
                    <th style="width: 15%;">Bill Number</th>
                    <th class="text-right" style="width: 15%;">Bill Amount</th>
                    <th class="text-right" style="width: 15%;">Amount Paid</th>
                    <th class="text-right" style="width: 15%;">Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ledgerEntries as $index => $entry)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ date('d F, Y', strtotime($entry->transaction_date)) }}</td>
                    <td>{{ $entry->bill_number }}</td>
                    <td class="text-right">{{ number_format($entry->bill_amount, 2) }}</td>
                    <td class="text-right">{{ number_format($entry->amount_paid, 2) }}</td>
                    <td class="text-right">
                        <span class="{{ $entry->running_balance > 0 ? 'balance-positive' : 'balance-negative' }}">
                            {{ number_format($entry->running_balance, 2) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="no-data">
            No ledger entries found for this customer.
        </div>
        @endif
    </div>

    @if($ledgerEntries->count() > 0)
    <table class="summary-table">
        <tr>
            <th>Total Bill Amount:</th>
            <td class="text-right">{{ number_format($totalBillAmount, 2) }}</td>
        </tr>
        <tr>
            <th>Total Paid Amount:</th>
            <td class="text-right">{{ number_format($totalPaidAmount, 2) }}</td>
        </tr>
        <tr class="total-due {{ $totalDueAmount > 0 ? 'negative' : '' }}">
            <th>Final Balance:</th>
            <td class="text-right">
                <strong>{{ number_format($totalDueAmount, 2) }}</strong>
            </td>
        </tr>
    </table>
    @endif

   
</body>
</html>