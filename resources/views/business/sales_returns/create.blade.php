@extends('layouts.business')

@section('content')
<div class="mb-4">
    <h2>Create Credit Note</h2>
    <p class="text-muted">Record a sales return and issue a credit note.</p>
</div>

<div class="card p-4">
    <form action="{{ route('business.sales-returns.store') }}" method="POST" id="returnForm">
        @csrf
        <div class="row mb-4">
            <div class="col-md-4">
                <label class="form-label">Customer</label>
                <select name="customer_id" class="form-select select2" required>
                    <option value="">Select Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Invoice Reference (Optional)</label>
                <select name="invoice_id" class="form-select select2">
                    <option value="">Select Invoice</option>
                    @foreach($invoices as $invoice)
                        <option value="{{ $invoice->id }}">{{ $invoice->invoice_number }} (₹{{ $invoice->total_amount }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Return Date</label>
                <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
        </div>

        <h5 class="mb-3">Returned Items</h5>
        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th width="150">Return Qty</th>
                        <th width="150">Unit Price</th>
                        <th width="150">Tax Amount</th>
                        <th width="150">Total</th>
                        <th width="50"></th>
                    </tr>
                </thead>
                <tbody id="itemsList">
                    <tr class="item-row">
                        <td>
                            <select name="items[0][product_id]" class="form-select product-select" required>
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-gst="{{ $product->gst_percentage ?? 0 }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="items[0][quantity]" class="form-control qty-input" min="1" value="1" required></td>
                        <td><input type="number" name="items[0][price]" class="form-control price-input" step="0.01" required></td>
                        <td><input type="number" name="items[0][tax_amount]" class="form-control tax-input" readonly></td>
                        <td><input type="number" name="items[0][total]" class="form-control total-input" readonly></td>
                        <td><button type="button" class="btn btn-sm btn-danger remove-item"><i class="fas fa-times"></i></button></td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn btn-sm btn-outline-primary" id="addItem"><i class="fas fa-plus me-2"></i>Add Another Item</button>
        </div>

        <div class="row">
            <div class="col-md-6">
                <label class="form-label">Reason for Return</label>
                <textarea name="reason" class="form-control" rows="4" placeholder="Optional notes..."></textarea>
            </div>
            <div class="col-md-6 text-end">
                <div class="d-flex justify-content-end mb-2">
                    <span class="text-muted me-3">Subtotal:</span>
                    <strong id="displaySubtotal">₹0.00</strong>
                    <input type="hidden" name="subtotal" id="inputSubtotal" value="0">
                </div>
                <div class="d-flex justify-content-end mb-3">
                    <span class="text-muted me-3">Total Tax:</span>
                    <strong id="displayTax">₹0.00</strong>
                    <input type="hidden" name="tax_amount" id="inputTax" value="0">
                </div>
                <div class="d-flex justify-content-end fs-5">
                    <span class="fw-bold me-3">Total:</span>
                    <strong class="text-primary" id="displayTotal">₹0.00</strong>
                    <input type="hidden" name="total_amount" id="inputTotal" value="0">
                </div>
            </div>
        </div>

        <div class="mt-4 text-end">
            <a href="{{ route('business.sales-returns.index') }}" class="btn btn-light me-2">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Credit Note</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({ theme: 'bootstrap-5' });
        let rowIdx = 1;

        $('#addItem').click(function() {
            const html = `
                <tr class="item-row">
                    <td>
                        <select name="items[${rowIdx}][product_id]" class="form-select product-select" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-gst="{{ $product->gst_percentage ?? 0 }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" name="items[${rowIdx}][quantity]" class="form-control qty-input" min="1" value="1" required></td>
                    <td><input type="number" name="items[${rowIdx}][price]" class="form-control price-input" step="0.01" required></td>
                    <td><input type="number" name="items[${rowIdx}][tax_amount]" class="form-control tax-input" readonly></td>
                    <td><input type="number" name="items[${rowIdx}][total]" class="form-control total-input" readonly></td>
                    <td><button type="button" class="btn btn-sm btn-danger remove-item"><i class="fas fa-times"></i></button></td>
                </tr>
            `;
            $('#itemsList').append(html);
            rowIdx++;
        });

        $(document).on('click', '.remove-item', function() {
            if ($('.item-row').length > 1) {
                $(this).closest('tr').remove();
                calculateTotals();
            }
        });

        $(document).on('change', '.product-select', function() {
            const opt = $(this).find('option:selected');
            const row = $(this).closest('tr');
            row.find('.price-input').val(opt.data('price'));
            calculateRow(row, opt.data('gst'));
            calculateTotals();
        });

        $(document).on('input', '.qty-input, .price-input', function() {
            const row = $(this).closest('tr');
            const opt = row.find('.product-select option:selected');
            calculateRow(row, opt.data('gst'));
            calculateTotals();
        });

        function calculateRow(row, gstRate) {
            const qty = parseFloat(row.find('.qty-input').val()) || 0;
            const price = parseFloat(row.find('.price-input').val()) || 0;
            const gst = parseFloat(gstRate) || 0;
            
            const sub = qty * price;
            const tax = sub * (gst / 100);
            const total = sub + tax;

            row.find('.tax-input').val(tax.toFixed(2));
            row.find('.total-input').val(total.toFixed(2));
        }

        function calculateTotals() {
            let subtotal = 0;
            let tax = 0;
            let total = 0;

            $('.item-row').each(function() {
                const qty = parseFloat($(this).find('.qty-input').val()) || 0;
                const price = parseFloat($(this).find('.price-input').val()) || 0;
                const rTax = parseFloat($(this).find('.tax-input').val()) || 0;
                
                subtotal += (qty * price);
                tax += rTax;
                total += (qty * price) + rTax;
            });

            $('#displaySubtotal').text('₹' + subtotal.toFixed(2));
            $('#inputSubtotal').val(subtotal.toFixed(2));
            
            $('#displayTax').text('₹' + tax.toFixed(2));
            $('#inputTax').val(tax.toFixed(2));
            
            $('#displayTotal').text('₹' + total.toFixed(2));
            $('#inputTotal').val(total.toFixed(2));
        }
    });
</script>
@endsection
