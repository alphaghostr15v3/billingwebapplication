@extends('layouts.business')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Inventory Management</h2>
    <div>
        <button class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="fas fa-folder-plus me-2"></i>New Category
        </button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="fas fa-plus me-2"></i>Add New Product
        </button>
    </div>
</div>

<div class="card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle" id="productsTable">
            <thead class="table-light">
                <tr>
                    <th>SKU</th>
                    <th>Product Name</th>
                    <th>HSN</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td><code class="bg-light px-2 py-1 rounded">{{ $product->sku }}</code></td>
                    <td>
                        <div class="fw-semibold">{{ $product->name }}</div>
                    </td>
                    <td><span class="text-muted">{{ $product->hsn_number ?? '-' }}</span></td>
                    <td>{{ $product->category->name }}</td>
                    <td>₹{{ number_format($product->price, 2) }}</td>
                    <td>
                        <span class="fw-bold {{ $product->stock_quantity <= $product->low_stock_limit ? 'text-danger' : '' }}">
                            {{ $product->stock_quantity }}
                        </span>
                    </td>
                    <td>
                        @if($product->stock_quantity <= $product->low_stock_limit)
                            <span class="badge bg-danger">Low Stock</span>
                        @else
                            <span class="badge bg-success">In Stock</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary edit-product" 
                            data-id="{{ $product->id }}"
                            data-name="{{ $product->name }}"
                            data-sku="{{ $product->sku }}"
                            data-hsn="{{ $product->hsn_number }}"
                            data-category="{{ $product->category_id }}"
                            data-price="{{ $product->price }}"
                            data-gst="{{ $product->gst_percentage }}"
                            data-stock="{{ $product->stock_quantity }}"
                            data-limit="{{ $product->low_stock_limit }}"
                            data-bs-toggle="modal" data-bs-target="#editProductModal">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('business.products.destroy', $product->id) }}" method="POST" class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('business.categories.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Electronics" required>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('business.products.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">SKU (Unique Identifier)</label>
                            <input type="text" name="sku" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Unit Price (₹)</label>
                            <input type="number" step="0.01" name="price" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">HSN Number</label>
                            <input type="text" name="hsn_number" class="form-control" placeholder="e.g. 8471">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">GST Percentage (%)</label>
                            <input type="number" step="0.01" name="gst_percentage" class="form-control" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Stock Quantity</label>
                            <input type="number" name="stock_quantity" class="form-control" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Low Stock Alert at</label>
                            <input type="number" name="low_stock_limit" class="form-control" value="5">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editProductForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku" id="edit_sku" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select name="category_id" id="edit_category" class="form-select" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Unit Price (₹)</label>
                            <input type="number" step="0.01" name="price" id="edit_price" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">HSN Number</label>
                            <input type="text" name="hsn_number" id="edit_hsn" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">GST Percentage (%)</label>
                            <input type="number" step="0.01" name="gst_percentage" id="edit_gst" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Stock Quantity</label>
                            <input type="number" name="stock_quantity" id="edit_stock" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Low Stock Alert at</label>
                            <input type="number" name="low_stock_limit" id="edit_limit" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Product</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#productsTable').DataTable();

        $('.edit-product').click(function() {
            const id = $(this).data('id');
            $('#edit_name').val($(this).data('name'));
            $('#edit_sku').val($(this).data('sku'));
            $('#edit_hsn').val($(this).data('hsn'));
            $('#edit_category').val($(this).data('category'));
            $('#edit_price').val($(this).data('price'));
            $('#edit_gst').val($(this).data('gst'));
            $('#edit_stock').val($(this).data('stock'));
            $('#edit_limit').val($(this).data('limit'));
            $('#editProductForm').attr('action', `/business/products/${id}`);
        });

        $('.delete-form').submit(function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "Deleting this product will not affect previous invoices.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
</script>
@endsection
