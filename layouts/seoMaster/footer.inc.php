<!-- Footer Start -->
<div class="container-fluid bg-primary text-light footer mt-5 pt-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container py-5 px-lg-5">
        <div class="row g-5">
            <div class="col-md-6 col-lg-3">
                <h5 class="text-white mb-4">Get In Touch</h5>
                <?php
                $footerAddr  = trim((string) config('app.site_postal_address'));
                $footerTel   = trim((string) config('app.site_contact_tel'));
                $footerEmail = trim((string) config('app.appEmail'));
                ?>
                <?php if ($footerAddr !== ''): ?><p><i class="fas fa-map-marker-alt me-3"></i><?= htmlspecialchars($footerAddr) ?></p><?php endif; ?>
                <?php if ($footerTel !== ''): ?><p><i class="fas fa-phone me-3"></i><a class="text-reset" href="tel:<?= htmlspecialchars(preg_replace('/[^0-9+]/', '', $footerTel)) ?>"><?= htmlspecialchars($footerTel) ?></a></p><?php endif; ?>
                <?php if ($footerEmail !== ''): ?><p><i class="fas fa-envelope me-3"></i><a class="text-reset" href="mailto:<?= htmlspecialchars($footerEmail) ?>"><?= htmlspecialchars($footerEmail) ?></a></p><?php endif; ?>
                <div class="d-flex pt-2">
                    <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-twitter"></i></a>
                    <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-youtube"></i></a>
                    <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-instagram"></i></a>
                    <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <h5 class="text-white mb-4">Popular Link</h5>
                <a class="btn btn-link" href="<?=$this->config->getFileRootPath()?>about">About Us</a>
                <a class="btn btn-link" href="<?=$this->config->getFileRootPath()?>services">Our Services</a>
                <a class="btn btn-link" href="<?=$this->config->getFileRootPath()?>feedback">Contact Us</a>
                <?php if (config('app.modules.testimonials') === 'on'): ?>
                <a class="btn btn-link" href="<?=$this->config->getFileRootPath()?>testimonials">Leave review</a>
                <?php endif; ?>
            </div>
            <?php
            // Footer "Project Gallery" thumbnails: prefer the featured / most-recent gallery
            // album; if there are NO albums, fall back to loose images in the gallery root
            // folder (the same images the home slider uses).
            $footerGalleryImages = [];   // each: ['src' => url, 'link' => url]
            if (config('app.modules.gallery') === 'on') {
                $fgBase = $this->config->getFileRootPath();
                $fg = container(\Dorguzen\Modules\Gallery\Services\GalleryService::class)->footerGalleryPayload();
                if ($fg) {
                    $fgAlbumId = (int) $fg['album']['album_id'];
                    $fgLink    = $fgBase . 'gallery/album?albumId=' . $fgAlbumId;
                    foreach (array_slice($fg['images'], 0, 6) as $fgImg) {
                        $footerGalleryImages[] = [
                            'src'  => $fgBase . 'assets/images/gallery/' . $fgAlbumId . '/' . \Dorguzen\Core\DGZ_Uploader\DGZ_Upload::thumbName($fgImg['image_filename']),
                            'link' => $fgLink,
                        ];
                    }
                } else {
                    // No album yet — use loose images in assets/images/gallery/ (link to the gallery page).
                    $fgRootDir = rtrim(DGZ_BASE_PATH, '/') . '/assets/images/gallery';
                    $fgRootImgs = glob($fgRootDir . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE) ?: [];
                    foreach (array_slice($fgRootImgs, 0, 6) as $imgPath) {
                        $footerGalleryImages[] = [
                            'src'  => $fgBase . 'assets/images/gallery/' . basename($imgPath),
                            'link' => null,   // root-folder images aren't an album — not clickable
                        ];
                    }
                }
            }
            ?>
            <?php if (!empty($footerGalleryImages)): ?>
            <div class="col-md-6 col-lg-3">
                <h5 class="text-white mb-4">Project Gallery</h5>
                <div class="row g-2">
                    <?php foreach ($footerGalleryImages as $fgi): ?>
                    <div class="col-4">
                        <?php if (!empty($fgi['link'])): ?>
                        <a href="<?= htmlspecialchars($fgi['link']) ?>"><img class="img-fluid" src="<?= htmlspecialchars($fgi['src']) ?>" alt="Gallery image"></a>
                        <?php else: ?>
                        <img class="img-fluid" src="<?= htmlspecialchars($fgi['src']) ?>" alt="Gallery image">
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            <div class="col-md-6 col-lg-3">
                <h5 class="text-white mb-4">Newsletter</h5>
                <p>Stay updated with our latest news, projects, and updates. Subscribe to our newsletter today.</p>
                <div class="mt-3">
                    <button type="button"
                            class="btn btn-outline-light rounded-pill px-4 py-2 fw-semibold"
                            data-bs-toggle="modal"
                            data-bs-target="#subscribeModal">
                        <i class="fa fa-envelope me-2"></i>Subscribe Now
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="container px-lg-5">
        <div class="copyright">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    &copy; <?= htmlspecialchars(config('app.appName')) ?>, All Rights Reserved.
                    Powered By <a class="border-bottom" href="https://dorguzen.com/" target="_blank" rel="noopener">Dorguzen</a>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="footer-menu">
                        <a href="<?=$this->config->getFileRootPath()?>">Home</a>
                        <a href="<?=$this->config->getFileRootPath()?>privacy">Cookies</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Footer End -->


<!-- Back to Top -->
<a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top pt-2"><i class="bi bi-arrow-up"></i></a>

<!-- Subscribe Modal (available on every page) -->
<?php $_subscribeBase = $this->config->getFileRootPath(); ?>
<div class="modal fade" id="subscribeModal" tabindex="-1" aria-labelledby="subscribeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:12px; overflow:hidden;">
            <div class="modal-header bg-primary text-white border-0 py-4 px-4">
                <h5 class="modal-title fw-bold" id="subscribeModalLabel">
                    Subscribe to Our Newsletter
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-4">
                <p class="text-muted mb-4">Stay updated with our latest news and updates. Enter your details below.</p>
                <form action="<?= $_subscribeBase ?>subscribe" method="post" id="subscribeForm">
                    <input type="hidden" name="_csrf_token" value="<?= getCsrfToken() ?>">
                    <div class="mb-3">
                        <label for="sub_email" class="form-label fw-semibold">
                            Email address <span class="text-danger">*</span>
                        </label>
                        <input type="email" id="sub_email" name="subscriber_email"
                               class="form-control" placeholder="you@example.com" required>
                    </div>
                    <div class="mb-4">
                        <label for="sub_firstname" class="form-label fw-semibold">
                            First name <span class="text-muted fw-normal">(optional)</span>
                        </label>
                        <input type="text" id="sub_firstname" name="subscriber_firstname"
                               class="form-control" placeholder="Your first name">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg fw-semibold">
                            Subscribe
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <small class="text-muted">We respect your privacy. Unsubscribe at any time.</small>
            </div>
        </div>
    </div>
</div>
<!-- End Subscribe Modal -->
