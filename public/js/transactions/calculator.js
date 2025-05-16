document.addEventListener('DOMContentLoaded', function () {
  const container = document.getElementById('payment-container');
  const grandTotal = parseFloat(container.dataset.grandTotal);
  const paymentDisplayCash = document.getElementById('payment-display-cash');
  const paymentDisplayCredit = document.getElementById('payment-display');
  let inputValue = '';

  function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
  }

  function updateDisplay() {
    const angka = parseFloat(inputValue.replace(/[^0-9.]/g, '')) || 0;
    paymentDisplayCash.textContent = formatRupiah(angka);
  }
    paymentDisplayCredit.textContent = formatRupiah(grandTotal);

  function resetInput() {
    inputValue = '';
    updateDisplay();
  }

  resetInput();

  // Event untuk tombol angka dan titik
  document.querySelectorAll('.num-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const val = btn.getAttribute('data-value');
      if (val === '.' && inputValue.includes('.')) return;  // cegah lebih dari 1 titik
      inputValue += val;
      updateDisplay();
    });
  });

  // Tombol Clear
  document.getElementById('clear-btn').addEventListener('click', resetInput);

  // Tombol Backspace (hapus 1 karakter terakhir)
  const backspaceBtn = document.getElementById('backspace-btn');
  if (backspaceBtn) {
    backspaceBtn.addEventListener('click', () => {
      if (inputValue.length > 0) {
        inputValue = inputValue.slice(0, -1);
        updateDisplay();
      }
    });
  }

  // Tombol OK untuk submit pembayaran cash
  document.getElementById('ok-btn').addEventListener('click', () => {
    const angka = parseFloat(inputValue.replace(/[^0-9.]/g, ''));
    if (!angka || angka === 0) {
      Swal.fire('Error', 'Masukkan nominal pembayaran terlebih dahulu', 'error');
      return;
    }
    Swal.fire({
      title: 'Konfirmasi Pembayaran',
      text: `Apakah Anda yakin ingin membayar sebesar ${formatRupiah(angka)}?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Ya, Bayar!',
      cancelButtonText: 'Batal'
    }).then(result => {
      if (result.isConfirmed) {
        document.getElementById('payment-amount').value = angka;
        document.getElementById('payment-methode').value = 'cash';
        document.getElementById('payment-form').submit();
      }
    });
  });

  // Switch tab payment (cash / credit)
  const paymentTab = document.getElementById('paymentTab');
  paymentTab.addEventListener('shown.bs.tab', function (event) {
    resetInput();
    const newMethod = event.target.id === 'credit-tab' ? 'credit' : 'cash';
    document.getElementById('payment-methode').value = newMethod;

    // Selalu tampilkan grand total di #payment-display
    paymentDisplayCredit.textContent = formatRupiah(grandTotal);

    if (newMethod === 'cash') {
      resetInput(); // reset inputValue & tampilan pembayaran cash
    }
  });


  // Submit pembayaran credit
  document.getElementById('credit-submit-btn').addEventListener('click', () => {
    Swal.fire({
      title: 'Konfirmasi Pembayaran',
      text: `Apakah Anda yakin ingin membayar sebesar ${formatRupiah(grandTotal)} dengan kartu kredit?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Ya, Bayar!',
      cancelButtonText: 'Batal'
    }).then(result => {
      if (result.isConfirmed) {
        document.getElementById('payment-amount').value = grandTotal;
        document.getElementById('payment-methode').value = 'credit';
        document.getElementById('payment-form').submit();
      }
    });
  });
});
