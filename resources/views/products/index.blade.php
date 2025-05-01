<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Správa produktů</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>

            body {
                font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                line-height: 1.5;
                padding: 1.5rem;
                background-color: #f8f9fa;
                color: #333;
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
            .mb-4, .mb-6 {
                margin-bottom: 1rem;
            }
            .mt-4 {
                margin-top: 1rem;
            }
            .flex {
                display: flex;
            }
            .flex-wrap {
                flex-wrap: wrap;
            }
            .gap-4 {
                gap: 1rem;
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
            .px-2, .px-3, .px-4 {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
            .py-1, .py-2, .py-3 {
                padding-top: 0.25rem;
                padding-bottom: 0.25rem;
            }
            .bg-blue-500 {
                background-color: #3b82f6;
                color: white;
            }
            .bg-blue-600 {
                background-color: #2563eb;
                color: white;
            }
            .bg-gray-500 {
                background-color: #6b7280;
                color: white;
            }
            .bg-green-500 {
                background-color: #22c55e;
                color: white;
            }
            .bg-green-600 {
                background-color: #16a34a;
                color: white;
            }
            .text-white {
                color: white;
            }
            .ml-2 {
                margin-left: 0.5rem;
            }
            .text-blue-500 {
                color: #3b82f6;
            }
            .bg-green-100 {
                background-color: #dcfce7;
            }
            .border-green-400 {
                border-color: #4ade80;
            }
            .text-green-700 {
                color: #15803d;
            }
            .shadow {
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            }
            .hover\:bg-blue-600:hover {
                background-color: #2563eb;
            }
            .hover\:bg-gray-600:hover {
                background-color: #4b5563;
            }
            .hover\:bg-green-600:hover {
                background-color: #16a34a;
            }
            .transition {
                transition: all 0.2s ease;
            }
            .card {
                background-color: white;
                border-radius: 0.5rem;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                padding: 1.5rem;
                margin-bottom: 1.5rem;
            }
            .form-group {
                margin-bottom: 1rem;
            }
            .form-label {
                display: block;
                margin-bottom: 0.5rem;
                font-weight: 500;
            }
            .form-control {
                width: 100%;
                padding: 0.5rem 0.75rem;
                border: 1px solid #d1d5db;
                border-radius: 0.375rem;
                background-color: white;
                height: 38px;
                box-sizing: border-box;
            }
            .form-control:focus {
                outline: none;
                border-color: #3b82f6;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
            }
            .btn {
                display: inline-block;
                font-weight: 500;
                text-align: center;
                vertical-align: middle;
                cursor: pointer;
                padding: 0.5rem 1rem;
                border-radius: 0.375rem;
                transition: all 0.2s ease;
                height: 38px;
                line-height: 1.2;
                box-sizing: border-box;
            }
            .btn-primary {
                background-color: #3b82f6;
                color: white;
            }
            .btn-primary:hover {
                background-color: #2563eb;
            }
            .btn-secondary {
                background-color: #6b7280;
                color: white;
            }
            .btn-secondary:hover {
                background-color: #4b5563;
            }
            .btn-success {
                background-color: #22c55e;
                color: white;
            }
            .btn-success:hover {
                background-color: #16a34a;
            }
        </style>
    @endif
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #e5e7eb;
        }
        th {
            background-color: #f3f4f6;
            font-weight: 600;
            text-align: left;
            cursor: pointer;
            position: relative;
        }
        th:hover {
            background-color: #e5e7eb;
        }
        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        tbody tr:hover {
            background-color: #f3f4f6;
        }
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0;
        }
        .pagination-info {
            color: #6b7280;
            font-size: 14px;
        }
        .pagination-controls {
            display: flex;
            gap: 10px;
        }
        .prev-link, .next-link {
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            text-decoration: none;
            color: #374151;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
        }
        .prev-link:hover, .next-link:hover {
            background-color: #f3f4f6;
        }
        .prev-link.disabled, .next-link.disabled {
            color: #9ca3af;
            cursor: not-allowed;
        }

        .pagination-numbers {
            margin-top: 20px;
            text-align: center;
        }
        .page-number {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 3px;
            border: 1px solid #d1d5db;
            color: #374151;
            text-decoration: none;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
        }
        .page-number:hover {
            background-color: #f3f4f6;
        }
        .page-number.active {
            background-color: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
        .page-ellipsis {
            display: inline-block;
            padding: 5px 10px;
            margin: 0 3px;
        }
        .price-range {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .price-input {
            width: 100px;
            flex: 1;
        }
    </style>
</head>
<body class="p-6">
    <div class="container mx-auto">
        <h1 class="text-2xl font-bold mb-6">Správa produktů</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 shadow">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-6 card">
            <form action="{{ route('products.index') }}" method="GET">
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label for="brand_id" class="form-label">Značka:</label>
                        <select name="brand_id" id="brand_id" class="form-control">
                            <option value="">Všechny značky</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="material_id" class="form-label">Materiál:</label>
                        <select name="material_id" id="material_id" class="form-control">
                            <option value="">Všechny materiály</option>
                            @foreach($materials as $material)
                                <option value="{{ $material->id }}" {{ request('material_id') == $material->id ? 'selected' : '' }}>
                                    {{ $material->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="search" class="form-label">Hledat:</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control" placeholder="Kód nebo popis">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label class="form-label">Cenové rozpětí:</label>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <input type="number" name="min_price" id="min_price" value="{{ request('min_price') }}" class="form-control" placeholder="Od" min="0" step="0.01">
                            <span style="margin: 0 5px; flex-shrink: 0;">-</span>
                            <input type="number" name="max_price" id="max_price" value="{{ request('max_price') }}" class="form-control" placeholder="Do" min="0" step="0.01">
                        </div>
                    </div>

                    <div class="form-group" style="display: flex; align-items: flex-end; justify-content: flex-end;">
                        <input type="hidden" name="sort_by" value="{{ request('sort_by', 'id') }}">
                        <input type="hidden" name="sort_direction" value="{{ request('sort_direction', 'asc') }}">
                        <div style="display: flex; gap: 10px;">
                            <button type="submit" class="btn btn-primary transition hover:bg-blue-600">Filtrovat</button>
                            <a href="{{ route('products.index') }}" class="btn btn-secondary transition hover:bg-gray-600">Resetovat</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="mb-4">
            <a href="{{ route('products.export', request()->all()) }}" class="btn btn-success transition hover:bg-green-600 shadow">
                <i class="fas fa-file-export mr-2"></i> Export do CSV
            </a>
        </div>

        <div class="card" style="padding: 0; overflow: hidden;">
            <table>
                <thead>
                    <tr>
                        <th class="sort" data-field="id">ID</th>
                        <th class="sort" data-field="code">Kód</th>
                        <th class="sort" data-field="brand_id">Značka</th>
                        <th class="sort" data-field="material_id">Materiál</th>
                        <th class="sort" data-field="price">Cena</th>
                        <th>Popis</th>
                        <th>Akce</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td>{{ $product->code }}</td>
                            <td>{{ $product->brand->name }}</td>
                            <td>{{ $product->material->name }}</td>
                            <td>{{ number_format($product->price, 2, ',', ' ') }} Kč</td>
                            <td>{{ Str::limit($product->description, 50) }}</td>
                            <td>
                                <a href="{{ route('products.edit', $product) }}" class="text-blue-500 hover:underline">
                                    <i class="fas fa-edit mr-1"></i> Upravit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 card">
            <div class="pagination-container">
                <div class="pagination-info">
                    Zobrazeno {{ $products->firstItem() ?? 0 }} až {{ $products->lastItem() ?? 0 }} z {{ $products->total() }} výsledků
                </div>
                <div class="pagination-controls">
                    <div id="custom-pagination">
                        @if ($products->lastPage() > 1)
                            <div class="pagination-numbers">
                                @if ($products->currentPage() > 1)
                                    <a href="{{ $products->appends(request()->except('page'))->url(1) }}" class="page-number">&laquo;</a>
                                    <a href="{{ $products->appends(request()->except('page'))->previousPageUrl() }}" class="page-number">&lsaquo;</a>
                                @endif

                                @php
                                    $startPage = max($products->currentPage() - 2, 1);
                                    $endPage = min($startPage + 4, $products->lastPage());

                                    if ($endPage - $startPage < 4) {
                                        $startPage = max($endPage - 4, 1);
                                    }
                                @endphp

                                @if ($startPage > 1)
                                    <span class="page-ellipsis">...</span>
                                @endif

                                @for ($i = $startPage; $i <= $endPage; $i++)
                                    <a href="{{ $products->appends(request()->except('page'))->url($i) }}"
                                       class="page-number {{ $i == $products->currentPage() ? 'active' : '' }}">
                                        {{ $i }}
                                    </a>
                                @endfor

                                @if ($endPage < $products->lastPage())
                                    <span class="page-ellipsis">...</span>
                                @endif

                                @if ($products->currentPage() < $products->lastPage())
                                    <a href="{{ $products->appends(request()->except('page'))->nextPageUrl() }}" class="page-number">&rsaquo;</a>
                                    <a href="{{ $products->appends(request()->except('page'))->url($products->lastPage()) }}" class="page-number">&raquo;</a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sortLinks = document.querySelectorAll('.sort');
            const currentSortBy = "{{ request('sort_by', 'id') }}";
            const currentSortDirection = "{{ request('sort_direction', 'asc') }}";

            sortLinks.forEach(link => {
                const field = link.dataset.field;

                if (field === currentSortBy) {
                    link.textContent += currentSortDirection === 'asc' ? ' ↑' : ' ↓';
                }

                link.addEventListener('click', function() {
                    const form = document.querySelector('form');
                    const sortByInput = form.querySelector('input[name="sort_by"]');
                    const sortDirectionInput = form.querySelector('input[name="sort_direction"]');

                    sortByInput.value = field;

                    if (field === currentSortBy) {
                        sortDirectionInput.value = currentSortDirection === 'asc' ? 'desc' : 'asc';
                    } else {
                        sortDirectionInput.value = 'asc';
                    }

                    form.submit();
                });
            });
        });
    </script>
</body>
</html>