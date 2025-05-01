<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upravit produkt</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>

            body {
                font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                line-height: 1.5;
                padding: 1.5rem;
            }
            .container {
                max-width: 1200px;
                margin: 0 auto;
            }
            .p-6 {
                padding: 1.5rem;
            }
            .mx-auto {
                margin-left: auto;
                margin-right: auto;
            }
            .max-w-2xl {
                max-width: 42rem;
            }
            .mb-1, .mb-4, .mb-6 {
                margin-bottom: 1rem;
            }
            .mt-1 {
                margin-top: 0.25rem;
            }
            .block {
                display: block;
            }
            .flex {
                display: flex;
            }
            .w-full {
                width: 100%;
            }
            .justify-between {
                justify-content: space-between;
            }
            .text-2xl {
                font-size: 1.5rem;
                font-weight: bold;
            }
            .font-bold {
                font-weight: bold;
            }
            .rounded {
                border-radius: 0.25rem;
            }
            .border {
                border: 1px solid #ddd;
            }
            .border-red-500 {
                border-color: #ef4444;
            }
            .px-3, .px-4 {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
            .py-2 {
                padding-top: 0.5rem;
                padding-bottom: 0.5rem;
            }
            .bg-blue-500 {
                background-color: #3b82f6;
                color: white;
            }
            .bg-gray-500 {
                background-color: #6b7280;
                color: white;
            }
            .text-white {
                color: white;
            }
            .text-red-500 {
                color: #ef4444;
            }
            .text-sm {
                font-size: 0.875rem;
            }
        </style>
    @endif
</head>
<body class="p-6">
    <div class="container mx-auto max-w-2xl">
        <h1 class="text-2xl font-bold mb-6">Upravit produkt</h1>

        <form action="{{ route('products.update', $product) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="code" class="block mb-1">Kód produktu:</label>
                <input type="text" name="code" id="code" value="{{ old('code', $product->code) }}"
                       class="w-full border rounded px-3 py-2 @error('code') border-red-500 @enderror">
                @error('code')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="brand_id" class="block mb-1">Značka:</label>
                <select name="brand_id" id="brand_id"
                        class="w-full border rounded px-3 py-2 @error('brand_id') border-red-500 @enderror">
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                            {{ $brand->name }}
                        </option>
                    @endforeach
                </select>
                @error('brand_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="material_id" class="block mb-1">Materiál:</label>
                <select name="material_id" id="material_id"
                        class="w-full border rounded px-3 py-2 @error('material_id') border-red-500 @enderror">
                    @foreach($materials as $material)
                        <option value="{{ $material->id }}" {{ old('material_id', $product->material_id) == $material->id ? 'selected' : '' }}>
                            {{ $material->name }}
                        </option>
                    @endforeach
                </select>
                @error('material_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="price" class="block mb-1">Cena:</label>
                <input type="number" name="price" id="price" step="0.01" value="{{ old('price', $product->price) }}"
                       class="w-full border rounded px-3 py-2 @error('price') border-red-500 @enderror">
                @error('price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block mb-1">Popis:</label>
                <textarea name="description" id="description" rows="4"
                          class="w-full border rounded px-3 py-2 @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-between">
                <a href="{{ route('products.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Zpět</a>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Uložit změny</button>
            </div>
        </form>
    </div>
</body>
</html>
