const input = document.getElementById('searchInput');
const box = document.getElementById('searchSuggest');

function renderSuggest(items){
  if(!box) return;
  if(!items || !items.length){
    box.style.display='none'; box.innerHTML=''; return;
  }
  box.innerHTML = items.map(it => `
    <a href="index.php?action=product&id=${it.id}" class="d-flex align-items-center p-3 text-decoration-none suggest-item">
      <i class="fas fa-box me-3 suggest-icon"></i>
      <div class="flex-grow-1">
        <div class="fw-semibold">${it.name}</div>
        <small class="text-muted">${new Intl.NumberFormat('vi-VN').format(it.price)} ₫</small>
      </div>
      <i class="fas fa-arrow-right suggest-arrow"></i>
    </a>
  `).join('');
  box.style.display = 'block';
}

async function suggest(q){
  if(!q || q.length < 2){ renderSuggest([]); return; }
  try{
    const r = await fetch(`index.php?action=search_suggest&q=${encodeURIComponent(q)}`);
    const data = await r.json();
    renderSuggest(data);
  }catch(e){ renderSuggest([]); }
}

if(input){
  input.addEventListener('input', e => suggest(e.target.value));
  input.addEventListener('focus', e => suggest(e.target.value));
  document.addEventListener('click', e => {
    if(!box.contains(e.target) && e.target !== input){ renderSuggest([]); }
  });
}

document.querySelectorAll('.nav-item.dropdown').forEach(item => {
  item.addEventListener('mouseleave', () => {
    const dropdown = bootstrap.Dropdown.getInstance(item.querySelector('.dropdown-toggle'));
    if (dropdown) dropdown.hide();
  });
});
