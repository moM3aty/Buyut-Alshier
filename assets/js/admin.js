document.addEventListener('DOMContentLoaded', () => {
  // 1. فتح وقفل القائمة الجانبية
  const sidebar = document.getElementById('sidebar');
  const overlay = document.querySelector('.sidebar-overlay');
  document.querySelectorAll('[data-toggle="sidebar"]').forEach(btn => {
    btn.addEventListener('click', () => sidebar.classList.toggle('open'));
  });
  if (overlay) overlay.addEventListener('click', () => sidebar.classList.remove('open'));

  // 2. فلترة البحث الفورية (Live Search)
  const searchInput = document.getElementById('liveSearch');
  if (searchInput) {
    searchInput.addEventListener('keyup', function() {
      const term = this.value.toLowerCase();
      document.querySelectorAll('.searchable-row').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
      });
    });
  }

  // 3. المودال المخصص للحذف (تم إصلاح مشكلة الحذف الفوري)
  const deleteForms = document.querySelectorAll('form[data-confirm]');
  const modal = document.getElementById('deleteModal');
  let formToSubmit = null;

  if (modal && deleteForms.length > 0) {
    const modalCancel = modal.querySelector('.modal-cancel');
    const modalConfirm = modal.querySelector('.modal-confirm');

    // منع الإرسال المباشر وفتح المودال
    deleteForms.forEach(form => {
      form.addEventListener('submit', function(e) {
        e.preventDefault(); // 👈 هذا هو السر لمنع الحذف الفوري
        formToSubmit = this;
        modal.classList.add('active');
      });
    });

    // زر الإلغاء
    if (modalCancel) {
      modalCancel.addEventListener('click', (e) => {
        e.preventDefault();
        modal.classList.remove('active');
        formToSubmit = null;
      });
    }

    // زر التأكيد
    if (modalConfirm) {
      modalConfirm.addEventListener('click', (e) => {
        e.preventDefault();
        if (formToSubmit) {
          formToSubmit.submit(); // إرسال الفورم بعد التأكيد فقط
        }
      });
    }
  }

  // 4. تحويل الـ Alert التقليدي لـ Toast
  document.querySelectorAll('.alert').forEach(alert => {
    let type = alert.classList.contains('error') ? 'error' : 'success';
    let msg = alert.textContent.trim();
    if (msg) {
      let container = document.querySelector('.toast-container');
      if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
      }
      const toast = document.createElement('div');
      toast.className = `toast ${type}`;
      toast.innerHTML = `<span>${type === 'success' ? '✅' : '⚠️'}</span> ${msg}`;
      container.appendChild(toast);
      setTimeout(() => { toast.remove(); }, 3500);
    }
    alert.style.display = 'none'; // إخفاء الرسالة القديمة
  });
});