document.addEventListener('DOMContentLoaded', function () {
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });

  const avatarEdit = document.querySelector('.avatar-edit');
  const avatarImg = document.querySelector('.avatar');
  const fileInput = document.getElementById('avatarInput');

  if (avatarEdit && fileInput && avatarImg) {
    avatarEdit.addEventListener('click', () => fileInput.click());

    fileInput.addEventListener('change', async function () {
      if (!this.files || !this.files.length) return;
      const file = this.files[0];
      const fd = new FormData();
      fd.append('avatar', file);

      avatarEdit.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div>';
      avatarEdit.classList.add('disabled');

      try {
        const res = await fetch('index.php?action=account_avatar_upload', {
          method: 'POST',
          body: fd,
        });
        const data = await res.json();
        if (data && data.ok) {
          avatarImg.src = data.url;
        } else {
          alert(data.error || 'Upload failed');
        }
      } catch (e) {
        alert('Upload failed');
      } finally {
        avatarEdit.innerHTML = '<i class="fas fa-camera"></i>';
        avatarEdit.classList.remove('disabled');
        this.value = '';
      }
    });
  }

  const form = document.querySelector('form');
  if (form) {
    form.addEventListener('submit', function (e) {
      const emailInput = form.querySelector('input[name="email"]');
      if (emailInput && !emailInput.value) {
        e.preventDefault();
        alert('Vui long nhap dia chi email!');
        return;
      }
      const btn = form.querySelector('button[type="submit"]');
      if (btn) {
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Dang cap nhat...';
        btn.disabled = true;
      }
    });
  }
});
