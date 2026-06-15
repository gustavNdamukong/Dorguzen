<?php

namespace Dorguzen\Modules\Gallery\Views;

class adminManageImages extends \Dorguzen\Core\DGZ_AdminHtmlView
{
    public function show(array $viewModel = []): void
    {
        extract($viewModel); // $album, $images
        $base = $this->controller->config->getFileRootPath();

        if (!$album) {
            echo '<div class="container py-5 text-center text-muted"><p>Album not found.</p></div>';
            return;
        }

        $albumId = (int) $album['album_id'];
        $imgPath = $base . 'assets/images/gallery/' . $albumId . '/';
        ?>
        <style>
            .img-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 14px; }
            .img-card { border-radius: 10px; overflow: hidden; background: #fff; box-shadow: 0 2px 10px rgba(0,0,0,.08); }
            .img-card .thumb-wrap { position: relative; height: 150px; background: #eee; }
            .img-card .thumb-wrap img { width: 100%; height: 100%; object-fit: cover; display: block; }
            .img-card .img-actions { padding: 8px; display: flex; gap: 6px; align-items: center; justify-content: space-between; }
            .cover-star { color: var(--site-theme, #fd7e14); }
            .upload-zone { border: 2px dashed #ccc; border-radius: 12px; padding: 30px; text-align: center; background: #fafafa; transition: border-color .2s; }
            .upload-zone:hover { border-color: var(--site-theme, #fd7e14); }
            .upload-zone input[type=file] { display: none; }
            .upload-zone label { cursor: pointer; }
            .preview-strip { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
            .preview-strip img { width: 70px; height: 70px; object-fit: cover; border-radius: 6px; border: 2px solid #eee; }
        </style>

        <!-- Hero -->
        <div class="container-xxl py-5 bg-primary hero-header mb-5">
            <div class="container my-5 py-5 px-lg-5">
                <div class="row g-5 py-5">
                    <div class="col-12 text-center">
                        <h1 class="text-white animated zoomIn"><?= htmlspecialchars($album['album_name']) ?></h1>
                        <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>admin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a class="text-white" href="<?= $base ?>admin/gallery">Gallery</a></li>
                                <li class="breadcrumb-item text-white active">Manage Images</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <section class="py-4">
            <div class="container">
                <?php
                $slideInMenu = \Dorguzen\Core\DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
                $slideInMenu->show();
                ?>

                <!-- Upload form -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h5 class="mb-3">Upload Images</h5>
                        <form method="POST" action="<?= $base ?>admin/gallery/upload"
                              enctype="multipart/form-data" id="uploadForm">
                            <input type="hidden" name="_csrf_token" value="<?= getCsrfToken() ?>">
                            <input type="hidden" name="albumId" value="<?= $albumId ?>">

                            <p class="text-muted small mb-3">
                            <i class="fa fa-info-circle me-1"></i>
                            You can select multiple images at once. JPEG, PNG, WebP, and GIF are supported.
                            A thumbnail is generated automatically for each upload.
                        </p>
                        <div class="upload-zone" id="dropZone">
                                <input type="file" name="gallery_images[]" id="fileInput"
                                       accept="image/*" multiple>
                                <label for="fileInput">
                                    <i class="fa fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="mb-1 text-muted">Click to choose photos or drag & drop</p>
                                    <small class="text-muted">JPEG, PNG, WebP, GIF — multiple selection allowed</small>
                                </label>
                                <div class="preview-strip" id="previewStrip"></div>
                            </div>

                            <div id="captionFields" class="mt-3"></div>

                            <button type="submit" class="btn text-white mt-3" id="uploadBtn" disabled
                                    style="background: var(--site-theme, #fd7e14);">
                                <i class="fa fa-upload me-1"></i> Upload
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Images grid -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Images <span class="badge bg-secondary"><?= count($images) ?></span></h5>
                </div>

                <?php if (empty($images)): ?>
                    <div class="alert alert-info">No images yet. Upload some above.</div>
                <?php else: ?>
                    <div class="img-grid">
                        <?php foreach ($images as $img): ?>
                            <div class="img-card">
                                <div class="thumb-wrap">
                                    <img src="<?= $imgPath . htmlspecialchars($img['thumb_filename']) ?>"
                                         alt="<?= htmlspecialchars($img['image_caption'] ?? '') ?>">
                                    <?php if (!empty($album['album_cover']) && $album['album_cover'] === $img['image_filename']): ?>
                                        <span style="position:absolute;top:6px;right:6px;background:rgba(0,0,0,.45);color:#ffc107;border-radius:50%;padding:3px 6px;font-size:.75rem;"
                                              title="Current cover"><i class="fa fa-star"></i></span>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($img['image_caption'])): ?>
                                    <div style="padding: 4px 8px; font-size:.78rem; color:#666;"><?= htmlspecialchars($img['image_caption']) ?></div>
                                <?php endif; ?>
                                <div class="img-actions">
                                    <!-- Set as cover -->
                                    <form method="POST" action="<?= $base ?>admin/gallery/setCover"
                                          style="display:inline;">
                                        <input type="hidden" name="_csrf_token" value="<?= getCsrfToken() ?>">
                                        <input type="hidden" name="albumId" value="<?= $albumId ?>">
                                        <input type="hidden" name="filename" value="<?= htmlspecialchars($img['image_filename']) ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-warning p-1 px-2" title="Set as album cover"
                                                style="font-size:.75rem;">
                                            <i class="fa fa-star"></i>
                                        </button>
                                    </form>
                                    <!-- Delete -->
                                    <a href="<?= $base ?>admin/gallery/deleteImage?imageId=<?= (int) $img['image_id'] ?>&albumId=<?= $albumId ?>"
                                       class="btn btn-sm btn-outline-danger p-1 px-2"
                                       title="Delete image"
                                       style="font-size:.75rem;"
                                       onclick="return confirm('Delete this image?');">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="mt-4">
                    <a href="<?= $base ?>admin/gallery" class="btn btn-outline-secondary btn-sm">
                        &larr; Back to Albums
                    </a>
                </div>
            </div>
        </section>

        <script>
        (function () {
            var dropZone     = document.getElementById('dropZone');
            var fileInput    = document.getElementById('fileInput');
            var previewStrip = document.getElementById('previewStrip');
            var captionFields = document.getElementById('captionFields');
            var uploadBtn    = document.getElementById('uploadBtn');

            function handleFiles(files) {
                previewStrip.innerHTML = '';
                captionFields.innerHTML = '';
                if (!files.length) { uploadBtn.disabled = true; return; }
                uploadBtn.disabled = false;
                for (var i = 0; i < files.length; i++) {
                    (function (file, index) {
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            var img = document.createElement('img');
                            img.src = e.target.result;
                            previewStrip.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                        var wrapper = document.createElement('div');
                        wrapper.className = 'mb-2';
                        wrapper.innerHTML =
                            '<label class="form-label mb-1" style="font-size:.8rem;">' +
                            'Caption for <em>' + file.name + '</em> (optional)' +
                            '</label>' +
                            '<input type="text" name="captions[' + index + ']" ' +
                            'class="form-control form-control-sm" placeholder="Caption..." maxlength="500">';
                        captionFields.appendChild(wrapper);
                    })(files[i], i);
                }
            }

            fileInput.addEventListener('change', function () {
                handleFiles(this.files);
            });

            dropZone.addEventListener('dragover', function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropZone.style.borderColor = 'var(--site-theme, #fd7e14)';
                dropZone.style.background  = '#fff8f0';
            });

            dropZone.addEventListener('dragleave', function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropZone.style.borderColor = '#ccc';
                dropZone.style.background  = '#fafafa';
            });

            dropZone.addEventListener('drop', function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropZone.style.borderColor = '#ccc';
                dropZone.style.background  = '#fafafa';
                var files = e.dataTransfer.files;
                if (!files.length) return;
                // Assign dropped files to the input so the form submits them
                var dt = new DataTransfer();
                for (var i = 0; i < files.length; i++) { dt.items.add(files[i]); }
                fileInput.files = dt.files;
                handleFiles(files);
            });
        })();
        </script>
        <?php
    }
}
