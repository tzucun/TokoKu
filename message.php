<?php if(isset($_SESSION['message'])): ?>
<div class="alert alert-<?= isset($_SESSION['error']) ? 'error' : 'success' ?>">
    <?= $_SESSION['message'] ?>
    <?php unset($_SESSION['message'], $_SESSION['error']); ?>
</div>
<?php endif; ?>
