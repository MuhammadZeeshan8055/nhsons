<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - NH & Sons</title>
</head>

<style>
    #table-data {
        border-collapse: collapse;
        padding: 3px;
    }

    #table-data td, #table-data th {
        border: 1px solid black;
    }

    .invoice-box {
        font-family: 'Arial', sans-serif;
        padding: 20px;
    }

    .header {
        text-align: center;
        margin-bottom: 20px;
    }

    .header img {
        max-width: 150px;
    }

    .header h1 {
        margin: 0;
        font-size: 24px;
    }

    .header p {
        margin: 0;
        font-size: 14px;
    }
</style>

<body>
<div class="invoice-box">
    <!-- Header with logo and company name -->
    <div class="header">
        <img src="{{ asset('assets/img/nh_logo.png') }}" alt="NH Logo">
        <h1>NH & Sons</h1>
        <p>123 Business Street, Suite 456, Cityville, Country</p>
    </div>

    <!-- Invoice details -->
    <table border="0" id="table-data" width="100%">
        <tr>
            <td width="70px"><b>Invoice</b></td>
            <td width="">: {{ $product_masuk->id }}</td>
            <td width="30px"><b>Created</b></td>
            <td>: {{ $product_masuk->tanggal }}</td>
        </tr>

        <tr>
            <td><b>Contact</b></td>
            <td>: {{ $product_masuk->supplier->telepon }}</td>
            <td><b>Address</b></td>
            <td>: {{ $product_masuk->supplier->alamat }}</td>
        </tr>

        <tr>
            <td><b>Supplier</b></td>
            <td>: {{ $product_masuk->supplier->nama }}</td>
            <td>: {{ $product_masuk->total_price }}</td>
            {{-- <td><b>Email</b></td>
            <td>: {{ $product_masuk->supplier->email }}</td> --}}
        </tr>

        <tr>
            <td><b>Product</b></td>
            <td>: {{ $product_masuk->product->nama }}</td>
            <td><b>Quantity</b></td>
            <td>: {{ $product_masuk->qty }}</td>
        </tr>
    </table>

    <table border="0" width="80%">
        <tr align="right">
            <td>Best Regards,</td>
        </tr>
    </table>

    <table border="0" width="80%">
        <tr align="right">
            <td>NH & Sons</td>
        </tr>
    </table>
</div>
</body>
</html>
