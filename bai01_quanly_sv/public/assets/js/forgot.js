document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('forgotForm');
  const emailInput = document.getElementById('email');
  const emailError = document.getElementById('emailError');
  const submitBtn = document.getElementById('submitBtn');

  function validateEmail(value) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
  }

  emailInput.addEventListener('input', function () {
    const value = this.value.trim();
    if (value === '') {
      this.classList.remove('is-invalid');
      emailError.textContent = 'Vui long nhap dia chi email.';
    } else if (!validateEmail(value)) {
      this.classList.add('is-invalid');
      emailError.textContent = 'Vui long nhap dia chi email hop le.';
    } else {
      this.classList.remove('is-invalid');
    }
  });

  form.addEventListener('submit', function (e) {
    const value = emailInput.value.trim();
    let ok = true;

    if (value === '') {
      emailInput.classList.add('is-invalid');
      emailError.textContent = 'Vui long nhap dia chi email.';
      ok = false;
    } else if (!validateEmail(value)) {
      emailInput.classList.add('is-invalid');
      emailError.textContent = 'Vui long nhap dia chi email hop le.';
      ok = false;
    }

    if (!ok) {
      e.preventDefault();
      emailInput.focus();
      return;
    }

    const original = submitBtn.innerHTML;
    submitBtn.innerHTML = '<div class="spinner-border spinner-border-sm me-2"></div> Dang gui...';
    submitBtn.disabled = true;
    setTimeout(() => {
      submitBtn.innerHTML = original;
      submitBtn.disabled = false;
    }, 2000);
  });

  emailInput.focus();

  emailInput.addEventListener('focus', function () {
    this.parentElement.style.transform = 'translateY(-2px)';
  });

  emailInput.addEventListener('blur', function () {
    this.parentElement.style.transform = 'translateY(0)';
  });

  document.addEventListener('keydown', function (e) {
    if (e.ctrlKey && e.key === 'Enter') {
      form.dispatchEvent(new Event('submit'));
    }
  });

  emailInput.addEventListener('paste', function () {
    setTimeout(() => {
      this.dispatchEvent(new Event('input'));
    }, 0);
  });
});

if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/sw.js').catch(() => {});
}
