<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فاتورة</title>
    <style>
            /* @font-face {
        font-family: 'Amiri';
        font-style: normal;
        font-weight: 400; 
        src: url('{{ storage_path('fonts/Amiri-Regular.ttf') }}') format('truetype');
    } */
        body {
              font-family: 'Amiri', serif;
    direction: rtl;
            text-align: right;
            padding: 20px;
            font-size: 14px;
            line-height: 1.6;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            border: 1px solid #eee;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }

        table {
            width: 100%;
            line-height: inherit;
            text-align: right;
            border-collapse: collapse;
        }

        table td {
            padding: 5px;
            vertical-align: top;
        }

        table tr td:nth-child(2) {
            text-align: left;
        }

        table tr.top table td {
            padding-bottom: 20px;
        }

        table tr.information table td {
            padding-bottom: 40px;
        }

        table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        table tr.details td {
            padding-bottom: 20px;
        }

        table tr.item td {
            border-bottom: 1px solid #eee;
        }

        table tr.item.last td {
            border-bottom: none;
        }

        table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <h1>فاتورة</h1>

        <table>
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                رقم الفاتورة: {{ $invoice->bill_number }}<br>
                                التاريخ: {{ \Carbon\Carbon::parse($invoice->created_at)->format('Y-m-d') }}
                            </td>
                            <td>
                                اسم العميل: {{ $invoice->user->full_name }}<br>
                                البريد الإلكتروني: {{ $invoice->user->email }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td>المنتج</td>
                <td>السعر</td>
            </tr>

            @foreach($invoice->order->orderItems as $item)
            <tr class="item">
                <td>{{ $item->product->name }}</td>
                <td>{{ number_format($item->unit_price, 2) }} ريال</td>
            </tr>
            @endforeach

            <tr class="total">
                <td></td>
                <td>الإجمالي: {{ number_format($invoice->order->total_amount, 2) }} ريال</td>
            </tr>
        </table>
    </div>
</body>
</html>
