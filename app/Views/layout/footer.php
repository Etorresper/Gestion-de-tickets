    <?php if(isset($_SESSION['user_id'])): ?>
            </div> <!-- End Main Content col -->
        </div> <!-- End row -->
    </div> <!-- End container-fluid -->
    


    <?php else: ?>
    </div> <!-- End Login Container -->
    <?php endif; ?>

    <div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="<?= APP_URL ?>/assets/js/notifications.js"></script>
    <?php if(isset($_SESSION['user_id'])): ?>
    <script src="<?= APP_URL ?>/assets/js/dashboard.js"></script>
    <script src="<?= APP_URL ?>/assets/js/search.js"></script>

    <?php endif; ?>
</body>
</html>
