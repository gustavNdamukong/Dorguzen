<?php

namespace Dorguzen\Views;

class privacy extends \Dorguzen\Core\DGZ_HtmlView
{
    public function show(array $viewModel = []): void
    {
        $business = trim((string) config('app.appName')) ?: 'this website';
        $email    = trim((string) config('app.appEmail'));

        $this->addMetadata([
            '<title>Privacy &amp; Cookie Policy | ' . htmlspecialchars($business) . '</title>',
            '<meta name="description" content="How ' . htmlspecialchars($business) . ' collects, uses and protects your data, and how we use cookies.">',
            '<meta name="robots" content="noindex, follow">',
        ]);

        $base = $this->controller->config->getFileRootPath();
        ?>

        <!-- Hero Header Start -->
        <div class="container-xxl py-5 bg-primary hero-header mb-5">
            <div class="container my-5 py-5 px-lg-5">
                <div class="row g-5 py-5">
                    <div class="col-12 text-center">
                        <h1 class="text-white animated zoomIn">Privacy &amp; Cookie Policy</h1>
                        <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>">Home</a></li>
                                <li class="breadcrumb-item text-white active" aria-current="page">Privacy &amp; Cookies</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- Hero Header End -->

        <!-- Content Start -->
        <div class="container-xxl py-3">
            <div class="container px-lg-5">
                <div class="row justify-content-center">
                    <div class="col-lg-9">

                        <p class="text-muted">
                            This policy explains how <?= htmlspecialchars($business) ?> collects, uses
                            and protects any information you provide when you use this website, and how
                            we use cookies. We are committed to keeping your personal information safe
                            and handling it responsibly.
                        </p>

                        <h4 class="mt-4 mb-3">1. Information We Collect</h4>
                        <p class="text-muted">
                            We only collect information you choose to give us — for example, your name,
                            email address and phone number when you submit a contact form or enquiry.
                            We do not collect more than we need to respond to you and provide our
                            services.
                        </p>

                        <h4 class="mt-4 mb-3">2. How We Use Your Information</h4>
                        <p class="text-muted">
                            We use the information you provide to respond to your enquiries, provide the
                            services you request, and improve our website. We do not sell or rent your
                            personal information to third parties.
                        </p>

                        <h4 class="mt-4 mb-3">3. Our Use of Cookies</h4>
                        <p class="text-muted">
                            Cookies are small text files placed on your device to help the website work
                            and to remember your preferences (such as your acknowledgement of this
                            notice). The cookies we use are safe and are used only to improve your
                            experience — we do not use them to collect personal data without your
                            knowledge. You can disable cookies in your browser settings, though some
                            parts of the website may not work as intended if you do.
                        </p>

                        <h4 class="mt-4 mb-3">4. Keeping Your Information Secure</h4>
                        <p class="text-muted">
                            We take reasonable technical and organisational measures to protect your
                            information against unauthorised access, loss or misuse.
                        </p>

                        <h4 class="mt-4 mb-3">5. Third-Party Links</h4>
                        <p class="text-muted">
                            This website may contain links to other websites. This policy applies only
                            to <?= htmlspecialchars($business) ?>, so we encourage you to read the
                            privacy policies of any other sites you visit.
                        </p>

                        <h4 class="mt-4 mb-3">6. Your Rights</h4>
                        <p class="text-muted">
                            You may ask us what personal information we hold about you, request that we
                            correct it, or ask us to delete it, in line with applicable
                            data-protection law. To do so, please contact us using the details below.
                        </p>

                        <h4 class="mt-4 mb-3">7. Changes to This Policy</h4>
                        <p class="text-muted">
                            We may update this Privacy &amp; Cookie Policy from time to time. Any
                            changes will be posted on this page, so please review it periodically.
                        </p>

                        <h4 class="mt-4 mb-3">8. Contact Us</h4>
                        <p class="text-muted">
                            If you have any questions about this policy or how we handle your data, please contact us
                            <?php if ($email !== ''): ?>
                                at <a href="mailto:<?= htmlspecialchars($email) ?>"><?= htmlspecialchars($email) ?></a>.
                            <?php else: ?>
                                via our <a href="<?= $base ?>feedback">contact page</a>.
                            <?php endif; ?>
                        </p>

                    </div>
                </div>
            </div>
        </div>
        <!-- Content End -->

        <?php
    }
}
