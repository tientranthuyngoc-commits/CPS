document.addEventListener('DOMContentLoaded', function(){
  const filterButtons = document.querySelectorAll('.filter-btn');
  const orders = document.querySelectorAll('#orders-container .col-12');
  filterButtons.forEach(btn=>{
    btn.addEventListener('click', function(){
      filterButtons.forEach(b=>b.classList.remove('active'));
      this.classList.add('active');
      const f = this.getAttribute('data-filter');
      orders.forEach(o=>{ o.style.display = (f==='all' || o.getAttribute('data-status')===f) ? 'block' : 'none'; });
    });
  });
  document.querySelectorAll('.order-card').forEach(card=>{
    card.addEventListener('mouseenter', ()=> card.style.cursor='pointer');
    card.addEventListener('click', function(e){
      if(!e.target.closest('a,button')){
        const link=this.querySelector('a[href*=\"account_order_detail\"]'); if(link) window.location.href=link.href;
      }
    });
  });
});
