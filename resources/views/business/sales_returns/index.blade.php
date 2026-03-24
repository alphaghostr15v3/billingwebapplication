@extends('layouts.business')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Credit Notes (Sales Returns)</h2>
    <a href="{{ route('business.sales-returns.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Create Credit Note
    </a>
</div>

<div class="card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle" id="returnsTable">
            <thead class="table-light">
                <tr>
                    <th>Return #</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total Refund</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($salesReturns as $return)
                <tr>
                    <td><span class="fw-bold">{{ $return->return_number }}</span></td>
                    <td>{{ $return->customer->name }}</td>
                    <td>{{ $return->date->format('d M Y') }}</td>
                    <td>₹{{ number_format($return->total_amount, 2) }}</td>
                    <td>
                        <a href="{{ route('business.sales-returns.show', $return->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('business.sales-returns.download', $return->id) }}" class="btn btn-sm btn-outline-success" title="Download PDF">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                        <form action="{{ route('business.sales-returns.destroy', $return->id) }}" method="POST" class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Return">
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
        $('#returnsTable').DataTable({
            order: [[0, 'desc']]
        });

        $('.delete-form').submit(function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "Deleting this credit note will revert the stock update. This action cannot be undone!",
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
