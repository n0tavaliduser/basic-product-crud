<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Detail</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f5f5f5;
            color: #222;
        }

        .container {
            max-width: 720px;
            margin: 40px auto;
            padding: 24px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }

        .card {
            display: grid;
            gap: 16px;
        }

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        a {
            display: inline-block;
            padding: 10px 14px;
            border-radius: 8px;
            background: #1f6feb;
            color: #fff;
            text-decoration: none;
        }

        a.secondary {
            background: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Detail Produk</h1>

        <div class="card">
            <div><strong>Nama:</strong> {{ $product->name }}</div>
            <div><strong>Harga:</strong> Rp {{ number_format((float) $product->price, 2, ',', '.') }}</div>
            <div><strong>Stok:</strong> {{ $product->stock }}</div>
            <div><strong>Deskripsi:</strong> {{ $product->description }}</div>
        </div>

        <div class="actions">
            <a href="{{ route('products.edit', $product) }}">Edit</a>
            <a class="secondary" href="{{ route('products.index') }}">Kembali</a>
        </div>
    </div>
</body>
</html>
