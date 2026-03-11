@extends('layouts.business')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Invoices</h2>
    <a href="{{ route('business.invoices.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Create New Invoice
    </a>
</div>

<div class="card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle" id="invoicesTable">
            <thead class="table-light">
                <tr>
                    <th>Invoice #</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $invoice)
                <tr>
                    <td><span class="fw-bold">{{ $invoice->invoice_number }}</span></td>
                    <td>{{ $invoice->customer->name }}</td>
                    <td>{{ $invoice->created_at->format('d M Y') }}</td>
                    <td>₹{{ number_format($invoice->total_amount, 2) }}</td>
                    <td>
                        <span class="badge {{ $invoice->status === 'paid' ? 'bg-success' : 'bg-warning' }}">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('business.invoices.show', $invoice->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('business.invoices.show', $invoice->id) }}?print=1" class="btn btn-sm btn-outline-secondary" target="_blank" title="Print">
                            <i class="fas fa-print"></i>
                        </a>
                        <a href="{{ route('business.invoices.download', $invoice->id) }}" class="btn btn-sm btn-outline-success" title="Download PDF">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                        <form action="{{ route('business.invoices.destroy', $invoice->id) }}" method="POST" class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Invoice">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#invoicesTable').DataTable({
            order: [[0, 'desc']]
        });

        $('.delete-form').submit(function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "Deleting this invoice will restore the product stock. This action cannot be undone!",
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
