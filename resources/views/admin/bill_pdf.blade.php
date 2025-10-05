

<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>{{ __('messages.bill') }}</title>
    <style>
        @font-face {
            font-family: 'Amiri';
            src: url('{{ storage_path('fonts/Amiri-Regular.ttf') }}') format('truetype');
            font-weight: normal;
        }
        
        * {
            font-family: 'Amiri', sans-serif;
            direction: rtl;
            text-align: right;
        }
        
        body {
            margin: 0;
            padding: 20px;
            font-size: 14px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 28px;
            color: #2c3e50;
        }
        
        .info-section {
            margin-bottom: 20px;
            display: table;
            width: 100%;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 5px 10px;
            width: 30%;
        }
        
        .info-value {
            display: table-cell;
            padding: 5px 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th {
            background-color: #34495e;
            color: white;
            padding: 12px;
            text-align: right;
            border: 1px solid #2c3e50;
        }
        
        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: right;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .total-section {
            margin-top: 30px;
            text-align: left;
            font-size: 18px;
            font-weight: bold;
        }
        
        .total-amount {
            background-color: #e8f5e9;
            padding: 15px;
            border-radius: 5px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>فاتورة</h1>
    </div>
    
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">رقم الفاتورة:</div>
            <div class="info-value">{{ $invoice->bill_number }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">التاريخ:</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($invoice->created_at)->format('Y-m-d') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">اسم العميل:</div>
            <div class="info-value">{{ $invoice->user->full_name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">البريد الإلكتروني:</div>
            <div class="info-value">{{ $invoice->user->email }}</div>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>المنتج</th>
                <th>السعر</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->order->orderItems as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ number_format($item->unit_price, 2) }} ريال</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="total-section">
        <div class="total-amount">
            الإجمالي: {{ number_format($invoice->order->total_amount, 2) }} ريال
        </div>
    </div>
</body>
</html>