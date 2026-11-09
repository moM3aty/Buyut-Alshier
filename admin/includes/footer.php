  </div>
</main>

<!-- Modal للتأكيد -->
<div class="modal-overlay" id="deleteModal">
  <div class="modal-box">
    <div class="modal-icon">⚠️</div>
    <h3>تأكيد الحذف</h3>
    <p>هل أنت متأكد من رغبتك في حذف هذا العنصر؟ لا يمكن التراجع عن هذا الإجراء.</p>
    <div class="modal-actions">
      <button type="button" class="btn btn-outline modal-cancel">إلغاء</button>
      <button type="button" class="btn btn-danger modal-confirm">نعم، احذف</button>
    </div>
  </div>
</div>

<script src="../assets/js/admin.js?v=<?= time() ?>"></script>
</body>
</html>