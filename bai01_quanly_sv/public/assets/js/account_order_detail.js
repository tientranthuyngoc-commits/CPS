document.addEventListener('DOMContentLoaded', function(){
  document.querySelectorAll('.action-btn').forEach(btn=>{
    btn.addEventListener('click', function(e){
      if(this.textContent.includes('Hủy đơn hàng')){
        if(!confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')) e.preventDefault();
      }
    });
  });
});
