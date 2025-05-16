@extends('layout.app')

@section('content')
    <style>
        .credit-option.selected {
            border: 2px solid #0d6efd;
            box-shadow: 0 0 10px rgba(13, 110, 253, 0.5);
            transition: 0.3s;
        }
    </style>
    <div class="container mt-4" id="payment-container" data-grand-total="{{ $sale->grand_total }}">
        <div class="row">
            <!-- Left: Order Details -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h5>Order ID: {{ $sale->invoice_number }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th class="text-end">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sale->items as $item)
                                    <tr>
                                        <td>{{ $item->quantity }}x {{ $item->product->name }}</td>
                                        <td class="text-end">Rp{{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="table-secondary">
                                    <td><strong>Sub Total</strong></td>
                                    <td class="text-end">Rp{{ number_format($sale->total, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Tax (11%)</td>
                                    <td class="text-end">Rp{{ number_format($sale->tax, 2) }}</td>
                                </tr>
                                <tr class="table-light">
                                    <td><strong>Total</strong></td>
                                    <td class="text-end"><strong>Rp{{ number_format($sale->grand_total, 2) }}</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right: Payment Section -->
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6>Payment Amount</h6>
                            <h4 id="payment-display" class="text-success">Rp 0</h4>
                        </div>
                        <div class="text-end">
                            <small class="d-block">User</small>
                            <strong>{{ $sale->user->name }}</strong>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="paymentTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="cash-tab" data-bs-toggle="tab" data-bs-target="#cash"
                                    type="button" role="tab" aria-controls="cash" aria-selected="true">Cash</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="credit-tab" data-bs-toggle="tab" data-bs-target="#credit"
                                    type="button" role="tab" aria-controls="credit"
                                    aria-selected="false">Credit</button>
                            </li>
                        </ul>

                        <div class="tab-content pt-3">
                            <!-- Cash Tab -->
                            <div class="tab-pane fade show active" id="cash" role="tabpanel"
                                aria-labelledby="cash-tab">
                                <div class="mb-3">
                                    <h4 id="payment-display-cash" class="text-center text-success">Rp 0</h4>
                                </div>
                                <div class="row g-2">
                                    @foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9, 0, '00'] as $num)
                                        <div class="col-4">
                                            <button type="button" class="btn btn-outline-secondary w-100 py-3 num-btn"
                                                data-value="{{ $num }}">{{ $num }}</button>
                                        </div>
                                    @endforeach
                                    <div class="col-4">
                                        <button type="button" class="btn btn-danger w-100 py-3" id="clear-btn">C</button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="btn btn-warning w-100 py-3"
                                            id="backspace-btn">←</button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="btn btn-primary w-100 py-3" id="ok-btn">OK</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Credit Tab -->
                            <div class="tab-pane fade" id="credit" role="tabpanel" aria-labelledby="credit-tab">
                                <div class="text-center p-4">
                                    <h5>Credit Payment</h5>
                                    <div class="d-flex justify-content-center gap-3 flex-wrap mt-3"
                                        id="credit-card-options">
                                        <div class="card credit-option p-3" style="width: 150px; cursor: pointer;"
                                            data-card="Visa">
                                            <img src="{{ asset('img/credit/visa.png') }}" alt="Visa"
                                                class="img-fluid mb-2" style="max-height: 50px;">
                                            <p class="mb-0">Visa</p>
                                        </div>
                                        <div class="card credit-option p-3" style="width: 150px; cursor: pointer;"
                                            data-card="MasterCard">
                                            <img src="{{ asset('img/credit/mastercard.png') }}" alt="MasterCard"
                                                class="img-fluid mb-2" style="max-height: 50px;">
                                            <p class="mb-0">MasterCard</p>
                                        </div>
                                        <div class="card credit-option p-3" style="width: 150px; cursor: pointer;"
                                            data-card="BRI">
                                            <img src="{{ asset('img/credit/bri.jpg') }}" alt="BRI"
                                                class="img-fluid mb-2" style="max-height: 50px;">
                                            <p class="mb-0">BRI</p>
                                        </div>
                                        <div class="card credit-option p-3" style="width: 150px; cursor: pointer;"
                                            data-card="BCA">
                                            <img src="{{ asset('img/credit/bca.jpg') }}" alt="BCA"
                                                class="img-fluid mb-2" style="max-height: 50px;">
                                            <p class="mb-0">BCA</p>
                                        </div>
                                    </div>
                                    <button type="button" id="credit-submit-btn"
                                        class="btn btn-primary mt-4">OK</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <form id="payment-form" action="{{ route('confirmation.sale.transaction', $sale->id) }}"
                    method="POST">
                    @csrf
                    <input type="hidden" id="payment-amount" name="amount_paid" value="">
                    <input type="hidden" id="payment-methode" name="payment_methode" value="cash">
                    <div class="mb-3">
                        <label for="note" class="form-label">Catatan (optional)</label>
                        <textarea name="note" id="note" class="form-control" rows="2"></textarea>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/transactions/calculator.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const options = document.querySelectorAll('.credit-option');
            options.forEach(option => {
                option.addEventListener('click', () => {
                    options.forEach(opt => opt.classList.remove('selected'));
                    option.classList.add('selected');
                    // Optional: console.log(option.dataset.card);
                });
            });
        });
    </script>
@endsection
