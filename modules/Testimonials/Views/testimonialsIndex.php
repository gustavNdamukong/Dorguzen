<?php

namespace Dorguzen\Modules\Testimonials\Views;

class testimonialsIndex extends \Dorguzen\Core\DGZ_HtmlView
{
    public function show(array $viewModel = []): void
    {
        extract($viewModel);
        // $testimonials — array of approved testimonials with stars_html
        $base       = $this->controller->config->getFileRootPath();
        $themeColor = 'var(--site-theme, #1565C0)';
        $csrf       = getCsrfToken();
        ?>

        <style>
            .testimonials-hero {
                background: linear-gradient(135deg, var(--site-theme, #1565C0) 0%, #0d47a1 100%);
                padding: 60px 0 44px;
                margin-bottom: 0;
            }
            .nlm-tcard {
                background: var(--nlm-dark-2, #111118);
                border: 1px solid rgba(255,255,255,.07);
                border-radius: 16px;
                padding: 28px 24px 24px;
                height: 100%;
                display: flex;
                flex-direction: column;
                gap: 14px;
                transition: transform .2s ease, box-shadow .2s ease;
            }
            .nlm-tcard:hover {
                transform: translateY(-4px);
                box-shadow: 0 8px 28px rgba(0,0,0,.35);
            }
            .nlm-tcard-stars {
                font-size: 1.25rem;
                color: #ffc107;
                letter-spacing: 1px;
            }
            .nlm-tcard-quote {
                color: var(--nlm-text, #e4e4f0);
                font-size: .95rem;
                line-height: 1.65;
                flex: 1;
                margin: 0;
            }
            .nlm-tcard-author {
                display: flex;
                align-items: center;
                gap: 13px;
                margin-top: 4px;
            }
            .nlm-tcard-avatar {
                width: 46px;
                height: 46px;
                border-radius: 50%;
                background: var(--site-theme, #0d47a1);
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                font-size: 1.1rem;
                flex-shrink: 0;
            }
            .nlm-tcard-name {
                font-weight: 700;
                font-size: .92rem;
                color: var(--nlm-text, #e4e4f0);
            }
            .nlm-tcard-role {
                font-size: .8rem;
                color: var(--nlm-text-muted, #9494b0);
                margin-top: 1px;
            }

            /* Submit form */
            .nlm-submit-section {
                background: var(--nlm-dark-1, #0a0a10);
                padding: 60px 0;
            }
            .nlm-form-card {
                background: var(--nlm-dark-2, #111118);
                border: 1px solid rgba(255,255,255,.07);
                border-radius: 18px;
                padding: 36px 32px;
                max-width: 680px;
                margin: 0 auto;
            }
            .nlm-form-card .form-label {
                color: var(--nlm-text, #e4e4f0);
                font-weight: 600;
                font-size: .88rem;
            }
            .nlm-form-card .form-control,
            .nlm-form-card .form-select {
                background: rgba(255,255,255,.05);
                border: 1px solid rgba(255,255,255,.12);
                color: var(--nlm-text, #e4e4f0);
                border-radius: 8px;
            }
            .nlm-form-card .form-control::placeholder { color: var(--nlm-text-muted, #6a6a88); }
            .nlm-form-card .form-control:focus,
            .nlm-form-card .form-select:focus {
                background: rgba(255,255,255,.08);
                border-color: var(--site-theme, #1565C0);
                box-shadow: 0 0 0 3px rgba(21,101,192,.25);
                color: var(--nlm-text, #e4e4f0);
            }
            .nlm-form-card .form-select option { background: #111118; color: #e4e4f0; }

            /* Star picker */
            .star-picker { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 4px; }
            .star-picker input[type="radio"] { display: none; }
            .star-picker label {
                font-size: 2rem;
                color: rgba(255,255,255,.2);
                cursor: pointer;
                transition: color .15s;
                line-height: 1;
            }
            .star-picker input:checked ~ label,
            .star-picker label:hover,
            .star-picker label:hover ~ label {
                color: #ffc107;
            }
        </style>

        <!-- Hero -->
        <div class="testimonials-hero">
            <div class="container text-center text-white">
                <h1 class="fw-bold mb-2" style="font-size:2.4rem; letter-spacing:-.5px;">Client Testimonials</h1>
                <p class="mb-0 opacity-75" style="font-size:1.05rem;">Real feedback from clients we've had the privilege to work with</p>
            </div>
        </div>

        <!-- Approved testimonials grid -->
        <section style="background:var(--nlm-dark-1,#0a0a10); padding:60px 0 40px;">
            <div class="container">
                <?php if (empty($testimonials)): ?>
                    <div class="text-center py-5" style="color:var(--nlm-text-muted,#9494b0);">
                        <i class="fas fa-comments fa-3x mb-3 d-block opacity-25"></i>
                        <p class="fs-5">No testimonials yet — be the first to leave a review!</p>
                    </div>
                <?php else: ?>
                    <div class="text-center mb-5">
                        <span class="nlm-label" style="font-size:.78rem; font-weight:700; letter-spacing:2px; color:var(--site-theme,#1565C0); text-transform:uppercase;">Client Love</span>
                        <h2 class="fw-bold mt-2" style="color:var(--nlm-text,#e4e4f0);">What Our Clients <span style="background:linear-gradient(90deg,var(--site-theme,#1565C0),#42a5f5);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Say About Us</span></h2>
                    </div>
                    <div class="row g-4">
                        <?php foreach ($testimonials as $t): ?>
                            <div class="col-lg-4 col-md-6">
                                <div class="nlm-tcard">
                                    <div class="nlm-tcard-stars"><?= $t['stars_html'] ?></div>
                                    <p class="nlm-tcard-quote">
                                        "<?= htmlspecialchars(mb_substr($t['testimonial_comment'], 0, 200)) ?><?= mb_strlen($t['testimonial_comment']) > 200 ? '&hellip;' : '' ?>"
                                    </p>
                                    <div class="nlm-tcard-author">
                                        <div class="nlm-tcard-avatar">
                                            <?= strtoupper(mb_substr($t['testimonial_name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="nlm-tcard-name"><?= htmlspecialchars($t['testimonial_name']) ?></div>
                                            <?php
                                            $meta = array_filter([
                                                $t['testimonial_role']    ?? '',
                                                $t['testimonial_company'] ?? '',
                                            ]);
                                            if ($meta): ?>
                                                <div class="nlm-tcard-role"><?= htmlspecialchars(implode(', ', $meta)) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Submit form -->
        <?php
        $hasFlash = !empty($_SESSION['_dgz_success']) || !empty($_SESSION['_dgz_errors']);
        ?>
        <section class="nlm-submit-section">
            <div class="container">

                <!-- Toggle button -->
                <div class="text-center mb-4">
                    <button type="button"
                            class="btn px-5 fw-semibold"
                            style="background:var(--site-theme,#1565C0); color:#fff; border:none; border-radius:8px; padding-top:12px; padding-bottom:12px; font-size:.95rem;"
                            onclick="toggleReviewForm(this)">
                        <i class="fas fa-pen me-2"></i>Leave a Review
                    </button>
                </div>

                <!-- Flash messages (always visible so the user sees feedback) -->
                <?php if (!empty($_SESSION['_dgz_success'])): ?>
                    <?php foreach ($_SESSION['_dgz_success'] as $msg): ?>
                        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert" style="max-width:680px; margin:0 auto 16px;">
                            <?= htmlspecialchars($msg) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endforeach; unset($_SESSION['_dgz_success']); ?>
                <?php endif; ?>
                <?php if (!empty($_SESSION['_dgz_errors'])): ?>
                    <?php foreach ($_SESSION['_dgz_errors'] as $msg): ?>
                        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert" style="max-width:680px; margin:0 auto 16px;">
                            <?= $msg ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endforeach; unset($_SESSION['_dgz_errors']); ?>
                <?php endif; ?>

                <!-- Collapsible form — auto-open if there are flash messages -->
                <div id="reviewFormWrap" style="display:<?= $hasFlash ? 'block' : 'none' ?>;">
                <div class="nlm-form-card">
                    <h3 class="fw-bold mb-1" style="color:var(--nlm-text,#e4e4f0); font-size:1.5rem;">Leave a Review</h3>
                    <p class="mb-4" style="color:var(--nlm-text-muted,#9494b0); font-size:.9rem;">Your review will appear on the site once approved by our team.</p>

                    <form method="post" action="<?= $base ?>testimonials/submit" novalidate>
                        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrf) ?>">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="testimonial_name">Your Name <span style="color:#e53935;">*</span></label>
                                <input type="text" class="form-control" id="testimonial_name" name="testimonial_name"
                                       placeholder="Jane Smith" required maxlength="200">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="testimonial_email">Email <span style="color:var(--nlm-text-muted,#9494b0); font-weight:400;">(private, not shown)</span></label>
                                <input type="email" class="form-control" id="testimonial_email" name="testimonial_email"
                                       placeholder="jane@example.com" maxlength="200">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="testimonial_company">Company</label>
                                <input type="text" class="form-control" id="testimonial_company" name="testimonial_company"
                                       placeholder="Acme Corp" maxlength="200">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="testimonial_role">Job Title / Role</label>
                                <input type="text" class="form-control" id="testimonial_role" name="testimonial_role"
                                       placeholder="CEO" maxlength="100">
                            </div>
                        </div>

                        <!-- Star picker -->
                        <div class="mt-3">
                            <label class="form-label d-block">Rating <span style="color:#e53935;">*</span></label>
                            <div class="star-picker" id="starPicker">
                                <input type="radio" name="testimonial_rating" id="star5" value="5">
                                <label for="star5" title="5 stars">&#9733;</label>
                                <input type="radio" name="testimonial_rating" id="star4" value="4">
                                <label for="star4" title="4 stars">&#9733;</label>
                                <input type="radio" name="testimonial_rating" id="star3" value="3">
                                <label for="star3" title="3 stars">&#9733;</label>
                                <input type="radio" name="testimonial_rating" id="star2" value="2">
                                <label for="star2" title="2 stars">&#9733;</label>
                                <input type="radio" name="testimonial_rating" id="star1" value="1">
                                <label for="star1" title="1 star">&#9733;</label>
                            </div>
                            <small id="ratingHint" style="color:var(--nlm-text-muted,#9494b0); font-size:.8rem; display:none;">Please select a rating.</small>
                        </div>

                        <div class="mt-3">
                            <label class="form-label" for="testimonial_comment">Your Review <span style="color:#e53935;">*</span></label>
                            <textarea class="form-control" id="testimonial_comment" name="testimonial_comment"
                                      rows="5" placeholder="Tell us about your experience working with us…" required maxlength="2000"></textarea>
                        </div>

                        <button type="submit" class="btn mt-4 px-5 fw-semibold"
                                style="background:var(--site-theme,#1565C0); color:#fff; border:none; border-radius:8px; padding-top:12px; padding-bottom:12px; font-size:.95rem;">
                            <i class="fas fa-paper-plane me-2"></i>Submit Review
                        </button>
                    </form>
                </div>
                </div><!-- /reviewFormWrap -->
            </div>
        </section>

        <script>
        function toggleReviewForm(btn) {
            var wrap = document.getElementById('reviewFormWrap');
            var open = wrap.style.display !== 'none';
            wrap.style.display = open ? 'none' : 'block';
            btn.innerHTML = open
                ? '<i class="fas fa-pen" style="margin-right:.5rem;"></i>Leave a Review'
                : '<i class="fas fa-times" style="margin-right:.5rem;"></i>Close Form';
            if (!open) {
                wrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        // Validate rating selected before submit
        (function () {
            var form = document.querySelector('form[action$="testimonials/submit"]');
            if (!form) return;
            form.addEventListener('submit', function (e) {
                var checked = form.querySelector('input[name="testimonial_rating"]:checked');
                var hint    = document.getElementById('ratingHint');
                if (!checked) {
                    e.preventDefault();
                    if (hint) { hint.style.display = 'block'; }
                    document.getElementById('starPicker').scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    if (hint) { hint.style.display = 'none'; }
                }
            });
        }());
        </script>

        <?php
    }
}
