<?php 
if(isset($_SESSION['message'])): 
    $alertId = 'alert_' . uniqid();
?>
<div class="alert alert-<?= isset($_SESSION['error']) ? 'error' : 'success' ?>" id="<?= $alertId ?>">
    <?= $_SESSION['message'] ?>
    <?php 
    unset($_SESSION['message']); 
    if(isset($_SESSION['error'])) {
        unset($_SESSION['error']);
    }
    ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        var alert = document.getElementById('<?= $alertId ?>');
        if(alert) {
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 1000);
        }
    }, 5000);
});
</script>
<?php endif; ?>