 
            <!-- ==========================
            JS 
        =========================== -->
        <?php /*<script src="https://code.jquery.com/jquery-latest.min.js"></script>
        <script src="https://code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&amp;sensor=true"></script> */ ?>


            <!-- JavaScript Libraries -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>


        <script src="<?= assetVer('assets/wow/wow.min.js') ?>"></script>
        <script src="<?= assetVer('assets/js/easing/easing.min.js') ?>"></script>
        <script src="<?= assetVer('assets/waypoints/waypoints.min.js') ?>"></script>
        <script src="<?= assetVer('assets/owlcarousel/owl.carousel.min.js') ?>"></script>
        <script src="<?= assetVer('assets/js/isotope/isotope.pkgd.min.js') ?>"></script>
        <script src="<?= assetVer('assets/lightbox/js/lightbox.min.js') ?>"></script>

        <!-- Template Javascript -->
        <script>window.dgzAppName = <?= json_encode(config('app.appName') ?: 'dgz') ?>;</script>
        <script src="<?= assetVer('js/main.js') ?>"></script>