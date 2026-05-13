</div><!-- end admin-content -->
</div><!-- end admin-layout -->
<script>
function confirmDelete(url, msg) {
    if(confirm(msg || 'Are you sure you want to delete this item?')) {
        window.location.href = url;
    }
}
function showToast(msg, type='success') {
    const t = document.createElement('div');
    t.style.cssText='position:fixed;top:1.5rem;right:1.5rem;background:'+(type==='success'?'#dcfce7':'#fee2e2')+';color:'+(type==='success'?'#166534':'#991b1b')+';padding:.9rem 1.5rem;border-radius:8px;font-size:.875rem;z-index:9999;box-shadow:0 4px 12px rgba(0,0,0,.15);font-weight:600';
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(()=>t.remove(), 3500);
}
<?php if(isset($_GET['saved'])): ?>showToast('✅ Saved successfully!');<?php endif; ?>
<?php if(isset($_GET['deleted'])): ?>showToast('🗑️ Deleted successfully!','error');<?php endif; ?>
</script>
</body>
</html>
