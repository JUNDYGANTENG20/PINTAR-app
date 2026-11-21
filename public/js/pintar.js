// Interaksi kecil PINTAR. Semua fitur utama tetap berjalan tanpa JavaScript.
document.addEventListener('click', (e) => {
  const menuBtn = e.target.closest('#menu-btn');
  if (menuBtn) {
    const open = document.getElementById('nav').classList.toggle('open');
    menuBtn.setAttribute('aria-expanded', open);
    return;
  }
  const filterBtn = e.target.closest('#filters-toggle');
  if (filterBtn) {
    const open = document.getElementById('filters').classList.toggle('open');
    filterBtn.setAttribute('aria-expanded', open);
    filterBtn.lastChild.textContent = open ? ' Sembunyikan' : ' Tampilkan';
    return;
  }
  const copyBtn = e.target.closest('[data-copy]');
  if (copyBtn) {
    const text = copyBtn.dataset.copy;
    const done = () => { copyBtn.lastChild.textContent = ' Disalin'; setTimeout(() => (copyBtn.lastChild.textContent = ' Salin'), 1800); };
    const fallback = () => { const r = document.createRange(); r.selectNodeContents(copyBtn.previousElementSibling); const s = getSelection(); s.removeAllRanges(); s.addRange(r); };
    if (navigator.clipboard) navigator.clipboard.writeText(text).then(done, fallback); else fallback();
    return;
  }
  // Tutup menu pengguna bila klik di luar
  document.querySelectorAll('details.user-menu[open]').forEach((d) => { if (!d.contains(e.target)) d.removeAttribute('open'); });
});

// Konfirmasi sebelum menghapus
document.addEventListener('submit', (e) => {
  const msg = e.target.dataset.confirm;
  if (msg && !window.confirm(msg)) e.preventDefault();
});

// Filter Jelajahi: kirim otomatis saat pilihan berubah
document.addEventListener('change', (e) => {
  const form = e.target.closest('form[data-autosubmit]');
  if (form && e.target.matches('select, input[type=checkbox]')) form.requestSubmit();
});

// Pratinjau poster di form admin
const posterInput = document.getElementById('poster');
if (posterInput) {
  posterInput.addEventListener('change', () => {
    const f = posterInput.files[0];
    const prev = document.getElementById('poster-prev');
    if (f && prev) { prev.innerHTML = ''; const img = document.createElement('img'); img.src = URL.createObjectURL(f); img.alt = 'Pratinjau poster'; prev.appendChild(img); }
  });
}
