@extends('layouts.business')

@section('content')
<div class="mb-4">
    <a href="{{ route('business.invoices.index') }}" class="text-decoration-none text-muted">
        <i class="fas fa-arrow-left me-2"></i>Back to Invoices
    </a>
    <h2 class="fw-bold mt-2">Create New Invoice</h2>
</div>

<form action="{{ route('business.invoices.store') }}" method="POST" id="invoiceForm">
    @csrf
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card p-4 h-100">
                <h5 class="fw-bold mb-3">Customer Details</h5>
                <div class="mb-3">
                    <label class="form-label">Select Customer</label>
                    <select name="customer_id" class="form-select" required>
                        <option value="">Choose a customer...</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ isset($selectedCustomerId) && $selectedCustomerId == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} ({{ $customer->phone }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Payment Method</label>
                    <select name="payment_method" class="form-select">
                        <option value="cash">Cash</option>
                        <option value="upi">UPI / Online</option>
                        <option value="card">Card</option>
                        <option value="credit">Credit / Unpaid</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card p-4">
                <h5 class="fw-bold mb-3">Invoice Items</h5>
                <div class="table-responsive">
                    <table class="table" id="itemsTable">
                        <thead>
                            <tr>
                                <th style="width: 40%">Product</th>
                                <th style="width: 15%">Price</th>
                                <th style="width: 15%">Qty</th>
                                <th style="width: 20%">Total</th>
                                <th style="width: 10%"></th>
                            </tr>
                        </thead>
                        <tbody id="invoiceItems">
                            <tr class="item-row">
                                <td>
                                    <select name="items[0][product_id]" class="form-select product-select" required>
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" 
                                                    data-price="{{ $product->price }}" 
                                                    data-gst="{{ $product->gst_percentage }}"
                                                    data-stock="{{ $product->stock_quantity }}">
                                                {{ $product->name }} (Stock: {{ $product->stock_quantity }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="items[0][price]" class="form-control item-price" step="0.01" readonly>
                                </td>
                                <td>
                                    <input type="number" name="items[0][quantity]" class="form-control item-qty" value="1" min="1" required>
                                </td>
                                <td>
                                    <input type="number" name="items[0][total]" class="form-control item-total" readonly>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-row"><i class="fas fa-times"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addRow">
                    <i class="fas fa-plus me-2"></i>Add Another Item
                </button>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card p-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Notes / Terms</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Additional info on the invoice..."></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <span class="fw-bold">₹<span id="subtotalDisplay">0.00</span></span>
                            <input type="hidden" name="subtotal" id="subtotalInput">
                        </div>
                        <div class="mb-2" id="taxBreakdown" style="display: none;">
                            <div class="d-flex justify-content-between small text-muted mb-1" id="cgstRow" style="display: none !important;">
                                <span>CGST:</span>
                                <span>₹<span id="cgstDisplay">0.00</span></span>
                            </div>
                            <div class="d-flex justify-content-between small text-muted mb-1" id="sgstRow" style="display: none !important;">
                                <span>SGST:</span>
                                <span>₹<span id="sgstDisplay">0.00</span></span>
                            </div>
                            <div class="d-flex justify-content-between small text-muted mb-1" id="igstRow" style="display: none !important;">
                                <span>IGST:</span>
                                <span>₹<span id="igstDisplay">0.00</span></span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Tax (GST):</span>
                            <span class="fw-bold">₹<span id="taxDisplay">0.00</span></span>
                            <input type="hidden" name="tax_amount" id="taxInput">
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Discount:</span>
                            <input type="number" name="discount_amount" id="discountInput" class="form-control form-control-sm w-25" value="0">
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="h5 fw-bold">Grand Total:</span>
                            <span class="h5 fw-bold text-primary">₹<span id="totalDisplay">0.00</span></span>
                            <input type="hidden" name="total_amount" id="totalInput">
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-save me-2"></i>Generate Invoice
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let rowCount = 1;

        const businessState = "{{ auth()->user()->business->state }}";
        const customerStates = {
            @foreach($customers as $customer)
                "{{ $customer->id }}": "{{ $customer->state }}",
            @endforeach
        };

        $('#addRow').click(function() {
            let newRow = $('.item-row:first').clone();
            newRow.find('input').val('');
            newRow.find('.item-qty').val(1);
            newRow.find('select').attr('name', `items[${rowCount}][product_id]`).val('');
            newRow.find('.item-price').attr('name', `items[${rowCount}][price]`);
            newRow.find('.item-qty').attr('name', `items[${rowCount}][quantity]`);
            newRow.find('.item-total').attr('name', `items[${rowCount}][total]`);
            $('#invoiceItems').append(newRow);
            rowCount++;
        });

        $(document).on('click', '.remove-row', function() {
            if ($('.item-row').length > 1) {
                $(this).closest('tr').remove();
                calculateTotals();
            }
        });

        $(document).on('change', '.product-select', function() {
            let row = $(this).closest('tr');
            let option = $(this).find('option:selected');
            let price = option.data('price') || 0;
            row.find('.item-price').val(price);
            calculateRow(row);
        });

        $(document).on('input', '.item-qty, #discountInput', function() {
            let row = $(this).closest('tr');
            calculateRow(row);
        });

        function calculateRow(row) {
            let qty = parseFloat(row.find('.item-qty').val()) || 0;
            let price = parseFloat(row.find('.item-price').val()) || 0;
            let total = qty * price;
            row.find('.item-total').val(total.toFixed(2));
            calculateTotals();
        }

        function calculateTotals() {
            let subtotal = 0;
            let taxTotal = 0;

            $('.item-row').each(function() {
                let row = $(this);
                let productSelect = row.find('.product-select');
                let option = productSelect.find('option:selected');
                let qty = parseFloat(row.find('.item-qty').val()) || 0;
                let price = parseFloat(row.find('.item-price').val()) || 0;
                let gst = parseFloat(option.data('gst')) || 0;

                let rowTotal = qty * price;
                subtotal += rowTotal;
                taxTotal += (rowTotal * gst) / 100;
            });

            let discount = parseFloat($('#discountInput').val()) || 0;
            let grandTotal = subtotal + taxTotal - discount;

            $('#subtotalDisplay').text(subtotal.toFixed(2));
            $('#subtotalInput').val(subtotal.toFixed(2));
            
            $('#taxDisplay').text(taxTotal.toFixed(2));
            $('#taxInput').val(taxTotal.toFixed(2));

            $('#totalDisplay').text(grandTotal.toFixed(2));
            $('#totalInput').val(grandTotal.toFixed(2));

            // Tax Breakdown
            const customerId = $('select[name="customer_id"]').val();
            const customerState = customerStates[customerId];

            $('#taxBreakdown').show();
            if (customerState && businessState === customerState) {
                $('#cgstDisplay').text((taxTotal / 2).toFixed(2));
                $('#sgstDisplay').text((taxTotal / 2).toFixed(2));
                $('#cgstRow, #sgstRow').attr('style', 'display: flex !important');
                $('#igstRow').attr('style', 'display: none !important');
            } else if (customerState) {
                $('#igstDisplay').text(taxTotal.toFixed(2));
                $('#igstRow').attr('style', 'display: flex !important');
                $('#cgstRow, #sgstRow').attr('style', 'display: none !important');
            } else {
                $('#taxBreakdown').hide();
            }
        }

        $('select[name="customer_id"]').change(function() {
            calculateTotals();
        });

        // Trigger calculation if a customer is pre-selected
        if ($('select[name="customer_id"]').val()) {
            calculateTotals();
        }
    });
</script>
@endsection
