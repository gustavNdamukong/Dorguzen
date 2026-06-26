<?php

namespace Dorguzen\Views;

class terms extends \Dorguzen\Core\DGZ_HtmlView
{
    public function show(array $viewModel = []): void
    {
        $business = trim((string) config('app.appName')) ?: 'this website';
        $email    = trim((string) config('app.appEmail'));

        $this->addMetadata([
            '<title>Terms &amp; Conditions | ' . htmlspecialchars($business) . '</title>',
            '<meta name="description" content="The terms and conditions governing the use of the ' . htmlspecialchars($business) . ' website.">',
            '<meta name="robots" content="noindex, follow">',
        ]);

        $base = $this->controller->config->getFileRootPath();
        ?>

        <!-- Hero Header Start -->
        <div class="container-xxl py-5 bg-primary hero-header mb-5">
            <div class="container my-5 py-5 px-lg-5">
                <div class="row g-5 py-5">
                    <div class="col-12 text-center">
                        <h1 class="text-white animated zoomIn">Terms &amp; Conditions</h1>
                        <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>">Home</a></li>
                                <li class="breadcrumb-item text-white active" aria-current="page">Terms &amp; Conditions</li>
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
                            Please read these Terms &amp; Conditions carefully before using the
                            <?= htmlspecialchars($business) ?> website. By accessing or using this
                            website, you agree to be bound by these terms. If you do not agree with
                            any part of them, please do not use the website.
                        </p>

                        <h4 class="mt-4 mb-3">1. Use of the Website</h4>
                        <p class="text-muted">
                            You may use this website for lawful purposes only. You agree not to use it
                            in any way that breaches any applicable law or regulation, or that may
                            damage, disable or impair the website or interfere with anyone else's use
                            of it.
                        </p>

                        <h4 class="mt-4 mb-3">2. Information We Provide</h4>
                        <p class="text-muted">
                            We take reasonable care to ensure the information on this website is
                            accurate and up to date, but we make no warranties as to its completeness
                            or accuracy. Any reliance you place on such information is strictly at your
                            own risk.
                        </p>

                        <h4 class="mt-4 mb-3">3. Intellectual Property</h4>
                        <p class="text-muted">
                            All content on this website — including text, graphics, logos and images —
                            is the property of <?= htmlspecialchars($business) ?> or its content
                            suppliers and is protected by applicable intellectual-property laws. You
                            may not reproduce or redistribute it without our prior written permission.
                        </p>

                        <h4 class="mt-4 mb-3">4. Links to Other Websites</h4>
                        <p class="text-muted">
                            This website may contain links to third-party websites that are not
                            operated by us. We have no control over, and accept no responsibility for,
                            the content or practices of those websites.
                        </p>

                        <h4 class="mt-4 mb-3">5. Limitation of Liability</h4>
                        <p class="text-muted">
                            To the fullest extent permitted by law, <?= htmlspecialchars($business) ?>
                            shall not be liable for any indirect or consequential loss or damage
                            arising out of your use of, or inability to use, this website.
                        </p>

                        <h4 class="mt-4 mb-3">6. Privacy &amp; Cookies</h4>
                        <p class="text-muted">
                            We use cookies to improve your experience on this website. By continuing to
                            use the site you consent to our use of cookies. We handle any personal
                            information you provide in line with applicable data-protection law.
                        </p>

                        <h4 class="mt-4 mb-3">7. Changes to These Terms</h4>
                        <p class="text-muted">
                            We may update these Terms &amp; Conditions from time to time. Any changes
                            will be posted on this page, so please review it periodically. Your
                            continued use of the website constitutes acceptance of the updated terms.
                        </p>

                        <h4 class="mt-4 mb-3">8. Governing Law</h4>
                        <p class="text-muted">
                            These terms are governed by and construed in accordance with the laws of
                            the jurisdiction in which <?= htmlspecialchars($business) ?> operates,
                            without regard to its conflict-of-law provisions.
                        </p>

                        <h4 class="mt-4 mb-3">9. Contact Us</h4>
                        <p class="text-muted">
                            If you have any questions about these Terms &amp; Conditions, please contact us
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
