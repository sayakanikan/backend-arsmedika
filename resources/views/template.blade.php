<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Arsmedika - Printout Transaction</title>
  <link rel="stylesheet" href="../../css/style.css" type="text/css">
  <style>
    *,
    *::before,
    *::after {
      box-sizing: border-box;
    }

    html {
      font-family: sans-serif;
      line-height: 1.15;
      -webkit-text-size-adjust: 100%;
      -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
    }

    body {
      margin: 0;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
      font-size: 1rem;
      font-weight: 400;
      line-height: 1.5;
      color: #1F1F1F;
      text-align: left;
      background-color: #fff;
    }
    
    h1, h2, h3, h4, h5, h6 {
      margin-top: 0;
      margin-bottom: 0.5rem;
    }

    p {
      margin-top: 0;
      margin-bottom: 1rem;
    }

    ol,
    ul,
    dl {
      margin-top: 0;
      margin-bottom: 1rem;
    }

    ol ol,
    ul ul,
    ol ul,
    ul ol {
      margin-bottom: 0;
    }

    table {
      border-collapse: collapse;
    }

    th {
      text-align: inherit;
      text-align: -webkit-match-parent;
    }

    .row {
      display: flex;
      flex-wrap: wrap;
      margin-right: -15px;
      margin-left: -15px;
    }

    .col-md-6{
      position: relative;
      width: 100%;
      padding-right: 15px;
      padding-left: 15px;
    }

    .col {
      flex-basis: 0;
      flex-grow: 1;
      max-width: 100%;
    }

    .table th, .jsgrid .jsgrid-table th,
    .table td,
    .jsgrid .jsgrid-table td {
      padding: 1.125rem 1.375rem;
      vertical-align: top;
      border-top: 1px solid #CED4DA;
    }

    .table thead th, .jsgrid .jsgrid-table thead th {
      vertical-align: bottom;
      border-bottom: 2px solid #CED4DA;
    }

    .table tbody + tbody, .jsgrid .jsgrid-table tbody + tbody {
      border-top: 2px solid #CED4DA;
    }

    .table-bordered {
      border: 1px solid #CED4DA;
    }

    .table-bordered th,
    .table-bordered td {
      border: 1px solid #CED4DA;
    }

    .table-bordered thead th,
    .table-bordered thead td {
      border-bottom-width: 2px;
    }

    .mb-2,
    .my-2 {
      margin-bottom: 0.5rem !important;
    }
  </style>
</head>
<body>
  <div class="row">
    <div class="col-md-6">
      <h1>PT. Arsmedika Sehat Berdikari</h1>
      <h4>Terdepan dalam mutu dan pelayanan</h4>
      <br>
      <p class="fw-medium">Klaim Jaminan Kesehatan</p>
      <p>Customer no : {{ $result->customer->customer_no }}</p>
      <p>Nama : {{ $result->customer->name }}</p>
      <p>Produk : {{ $result->customer->product->product_name }}</p>
      <p>Tanggal Klaim : {{ $result->created_at->format('d M Y') }}</p>
          <table class="table table-bordered mb-2">
            <thead>
              <tr>
                <td>Rincian</td>
                <td>Harga</td>
                <td>Kuantitas</td>
                <td>Total bayar</td>
                <td>Ditanggung</td>
                <td>Byr. Sendiri</td>
              </tr>
            </thead>
            <tbody>
              @foreach ($result->transaction_detail as $item)
              <tr>
                <td>{{ $item->product_detail->item }}</td>
                <td>{{ $item->payment_amount }}</td>
                @if ($item->quantity == null)
                  <td>1</td>
                @else
                  <td>{{ $item->quantity }}</td>
                @endif
                <td>{{ $item->total_payment }}</td>
                <td>{{ $item->covered }}</td>
                <td>{{ $item->customer_pay }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        
      <p>Total Ditanggung : Rp. {{ number_format($result->total_covered,0,',','.') }}</p>
      <p>Dibayar sendiri : Rp. {{ number_format($result->total_customer_pay,0,',','.') }}</p>
      <b>Harap langsung dibayar dikasir. Terimakasih.</b>
    </div>
  </div>
</body>
</html>