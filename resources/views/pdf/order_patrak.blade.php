<!DOCTYPE html>
<html>

<head>
    <style>
        /* @font-face {
            font-family: 'Gujarati';
            src: url('{{ public_path('fonts/NotoSansGujarati-Regular.ttf') }}') format('truetype');
        } */

        /* body {
            font-family: 'Gujarati', sans-serif;
            font-size: 12px;
        } */

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        th {
            font-weight: 900;
        }

        .title {
            text-align: center;
            font-weight: 900;
            font-size: 16px;
        }

        body {
            font-family: notosansdevanagari, notosansgujarati;
            font-size: 14px;
        }

        .gujarati {
            font-family: notosansgujarati;
        }
    </style>
</head>

<body>

    <p class="title">Order Form (માંગ પત્રક)</p>

    <p>
        Order to: ..... Name of Company (Sender) ................
    </p>
    <p>
        Date: {{ date('d-m-Y') }}
    </p>

    <p>
        Receiver and Destination (પ્રાપ્તકર્તા)
    </p>

    <p>
        Full Name: {{ $stokiestUser->name ?? '-' }}
    </p>

    <p>
        Mobile: {{ $stokiestUser->mobile_no ?? '-' }}
    </p>

    <p>
        Address: {{ $stokiestUser->address ?? '-' }}
    </p>

    <br>

    <table>
        <thead>
            <tr>
                <td style="color: red;">ક્રમ</td>
                <td>ઔષધ</td>
                <td>પ્રમાણ</td>
                <td style="color: red;">માંગ</td>
            </tr>
        </thead>
        <tbody>
            @php $i = 1; @endphp
            @foreach ($items as $item)
                @php
                    $qty = $medicineQtyMap[$item['medicine_id']] ?? '';
                @endphp
                <tr>
                    <td style="color:red">{{ $i++ }}</td>
                    <td style="color:#0096FF">{{ $item['name'] }}</td>
                    <td>{{ $item['packing'] }}</td>
                    <td>{{ $qty }}</td>
                </tr>
            @endforeach
        </tbody>

    </table>

    <br><br>

    <p style="text-align:right">
        Order by: ____________ <br>
        સેવાભારતી - ગુજરાત
    </p>

</body>

</html>
