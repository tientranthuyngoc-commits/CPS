document.addEventListener('DOMContentLoaded', function () {
  setInterval(() => {
    console.log('Refreshing dashboard stats...');
  }, 30000);

  document.querySelectorAll('.quick-action-card').forEach(card => {
    card.addEventListener('click', function () {
      const text = this.querySelector('div:last-child')?.textContent?.trim() || '';
      console.log('Quick action:', text);
    });
  });
});
