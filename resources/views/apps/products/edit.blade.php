<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
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

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
        }

        input,
        textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            box-sizing: border-box;
        }

        .actions {
            display: flex;
            gap: 12px;
        }

        a,
        button {
            display: inline-block;
            padding: 10px 14px;
            border: 0;
            border-radius: 8px;
            background: #1f6feb;
            color: #fff;
            text-decoration: none;
            cursor: pointer;
        }

        a {
            background: #6c757d;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            background: #fdecec;
            color: #991b1b;
        }

        ul {
            margin: 0;
            padding-left: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Produk</h1>

        @if ($errors->any())
            <div class="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('products.update', $product) }}" method="POST">
            @csrf
            @method('PUT')

            <label for="name">Nama</label>
            <input id="name" name="name" type="text" value="{{ old('name', $product->name) }}" required>

            <label for="price">Harga</label>
            <input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', $product->price) }}" required>

            <label for="stock">Stok</label>
            <input id="stock" name="stock" type="number" min="0" value="{{ old('stock', $product->stock) }}" required>

            <label for="description">Deskripsi</label>
            <textarea id="description" name="description" rows="5" required>{{ old('description', $product->description) }}</textarea>

            <div class="actions">
                <button type="submit">Update</button>
                <a href="{{ route('products.index') }}">Kembali</a>
            </div>
        </form>
    </div>
</body>
</html>
