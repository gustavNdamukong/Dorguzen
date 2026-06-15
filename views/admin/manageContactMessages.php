<?php

namespace Dorguzen\Views\Admin;


class manageContactMessages extends \Dorguzen\Core\DGZ_AdminHtmlView
{
     function show(array $viewModel = [])
     {
          extract($viewModel);
          ?>

          <!-- Hero Header Start -->
          <div class="container-xxl py-5 bg-primary hero-header mb-5">
               <div class="container my-5 py-5 px-lg-5">
                    <div class="row g-5 py-5">
                         <div class="col-12 text-center">
                              <h1 class="text-white animated zoomIn">Contact Messages</h1>
                              <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                              <nav aria-label="breadcrumb">
                                   <ol class="breadcrumb justify-content-center">
                                        <li class="breadcrumb-item"><a class="text-white" href="<?= $this->controller->config->getFileRootPath() ?>admin/dashboard">Dashboard</a></li>
                                        <li class="breadcrumb-item text-white active" aria-current="page">Contact Messages</li>
                                   </ol>
                              </nav>
                         </div>
                    </div>
               </div>
          </div>
          <!-- Hero Header End -->

          <style>
               .cm-table-wrap { border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.08); overflow: hidden; }
               .cm-table-wrap .table-responsive { border-radius: 12px; }
               .cm-table-wrap table { margin-bottom: 0; }
               .cm-table-wrap thead th {
                    background: var(--site-theme, #fd7e14) !important;
                    color: #fff !important;
                    font-size: .8rem;
                    font-weight: 700;
                    text-transform: uppercase;
                    letter-spacing: .5px;
                    padding: 12px 14px;
                    border: none !important;
                    white-space: nowrap;
               }
               .cm-table-wrap tbody td {
                    vertical-align: middle;
                    padding: 10px 14px;
                    font-size: .88rem;
                    border-color: #f0f0f0;
                    color: #343a40;
               }
               .cm-table-wrap tbody tr:hover { background: #fff8f2; }
               .cm-table-wrap tbody tr:nth-child(even) { background: #fafafa; }
               .cm-table-wrap tbody tr:nth-child(even):hover { background: #fff8f2; }
               .cm-truncate {
                    max-width: 160px;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
               }
               .cm-message { max-width: 280px; }

               /* View modal */
               #viewContactMessageModal .modal-header {
                    background: var(--site-theme, #fd7e14);
                    border-radius: 8px 8px 0 0;
                    padding: 16px 20px;
               }
               #viewContactMessageModal .modal-title {
                    color: #fff;
                    font-weight: 700;
                    font-size: 1.1rem;
               }
               #viewContactMessageModal .btn-close { filter: invert(1); }
               #viewContactMessageModal .modal-body { padding: 24px 28px 8px; }
               #viewContactMessageModal .cm-detail-label {
                    font-size: .75rem;
                    font-weight: 700;
                    text-transform: uppercase;
                    letter-spacing: .5px;
                    color: #6c757d;
                    margin-bottom: 2px;
               }
               #viewContactMessageModal .cm-detail-value {
                    font-size: .95rem;
                    color: #343a40;
                    margin-bottom: 16px;
                    word-break: break-word;
               }
               #viewContactMessageModal .cm-message-body {
                    background: #f8f9fa;
                    border-left: 4px solid var(--site-theme, #fd7e14);
                    border-radius: 4px;
                    padding: 14px 16px;
                    font-size: .92rem;
                    color: #343a40;
                    white-space: pre-wrap;
                    word-break: break-word;
               }
               #viewContactMessageModal .modal-footer { padding: 12px 28px 20px; border: none; }
          </style>

          <div class="container-xxl py-5">
               <div class="container px-lg-5">

                    <!-- START SIDE SLIDE-IN MENU -->
                    <?php
                    $jsValidation = \Dorguzen\Core\DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
                    $jsValidation->show();
                    ?>
                    <!-- END OF SIDE SLIDE-IN MENU -->

                    <div class="d-flex align-items-center justify-content-between mb-3">
                         <h2 class="mb-0">Contact Messages
                              <small class="text-muted fs-6 fw-normal ms-2">
                                   <?= count($contactMessages) ?> message<?= count($contactMessages) != 1 ? 's' : '' ?>
                              </small>
                         </h2>
                         <a href="<?= $this->controller->config->getFileRootPath() ?>admin/dashboard" class="btn btn-outline-secondary btn-sm">← Dashboard</a>
                    </div>

                    <?php if (!empty($contactMessages)) { ?>
                    <div class="cm-table-wrap">
                         <div class="table-responsive">
                              <table class="table table-hover align-middle">
                                   <thead>
                                        <tr>
                                             <th>Name</th>
                                             <th>Email</th>
                                             <th>Phone</th>
                                             <th>Message</th>
                                             <th>Date</th>
                                             <th class="text-center">Action</th>
                                        </tr>
                                   </thead>
                                   <tbody>
                                   <?php foreach ($contactMessages as $msg) {
                                        $mid      = (int) $msg['contactformmessage_id'];
                                        $cmName   = htmlspecialchars($msg['contactformmessage_name']    ?? '');
                                        $cmEmail  = htmlspecialchars($msg['contactformmessage_email']   ?? '');
                                        $cmPhone  = htmlspecialchars($msg['contactformmessage_phone']   ?? '');
                                        $cmMsg    = htmlspecialchars($msg['contactformmessage_message'] ?? '');
                                        $date     = htmlspecialchars($msg['contactformmessage_date']    ?? '');
                                        $datePart = explode(' ', $date)[0];
                                        ?>
                                        <tr>
                                             <td class="cm-truncate" title="<?= $cmName ?>">
                                                  <?= $cmName ?: '—' ?>
                                             </td>
                                             <td class="cm-truncate" title="<?= $cmEmail ?>">
                                                  <?= $cmEmail ?: '—' ?>
                                             </td>
                                             <td class="cm-truncate" title="<?= $cmPhone ?>">
                                                  <?= $cmPhone ?: '—' ?>
                                             </td>
                                             <td class="cm-truncate cm-message" title="<?= $cmMsg ?>">
                                                  <?= $cmMsg ?: '—' ?>
                                             </td>
                                             <td style="white-space:nowrap; font-size:.82rem; color:#6c757d;">
                                                  <?= $datePart ?>
                                             </td>
                                             <td class="text-center" style="white-space:nowrap;">
                                                  <button type="button"
                                                       class="btn btn-sm btn-primary cm-view-btn"
                                                       data-name="<?= $cmName ?>"
                                                       data-email="<?= $cmEmail ?>"
                                                       data-phone="<?= $cmPhone ?>"
                                                       data-message="<?= $cmMsg ?>"
                                                       data-date="<?= $datePart ?>"
                                                       data-bs-toggle="modal"
                                                       data-bs-target="#viewContactMessageModal">
                                                       View
                                                  </button>
                                                  <a href="<?= $this->controller->config->getFileRootPath() ?>admin/deleteContactMessage?contactformmessage_id=<?= $mid ?>"
                                                     class="btn btn-sm btn-danger"
                                                     onclick="return confirm('Are you sure you want to delete this message?')">
                                                       Delete
                                                  </a>
                                             </td>
                                        </tr>
                                   <?php } ?>
                                   </tbody>
                              </table>
                         </div>
                    </div>
                    <?php } else { ?>
                         <p class="text-muted">There are no contact messages yet.</p>
                    <?php } ?>

               </div>
          </div>

          <!-- VIEW CONTACT MESSAGE MODAL -->
          <div class="modal fade" id="viewContactMessageModal" tabindex="-1" aria-hidden="true">
               <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content" style="border-radius:12px; overflow:hidden; border:none; box-shadow:0 8px 32px rgba(0,0,0,.15);">
                         <div class="modal-header border-0">
                              <h5 class="modal-title">Contact Message</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                         </div>
                         <div class="modal-body">
                              <div class="row">
                                   <div class="col-sm-4">
                                        <p class="cm-detail-label">Name</p>
                                        <p class="cm-detail-value" id="cmViewName">—</p>
                                   </div>
                                   <div class="col-sm-4">
                                        <p class="cm-detail-label">Email</p>
                                        <p class="cm-detail-value" id="cmViewEmail">—</p>
                                   </div>
                                   <div class="col-sm-4">
                                        <p class="cm-detail-label">Phone</p>
                                        <p class="cm-detail-value" id="cmViewPhone">—</p>
                                   </div>
                              </div>
                              <p class="cm-detail-label">Message</p>
                              <div class="cm-message-body" id="cmViewMessage"></div>
                              <p class="cm-detail-label mt-3">Date</p>
                              <p class="cm-detail-value" id="cmViewDate">—</p>
                         </div>
                         <div class="modal-footer">
                              <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                         </div>
                    </div>
               </div>
          </div>
          <!-- END VIEW CONTACT MESSAGE MODAL -->

          <script>
          (function () {
               var modal = document.getElementById('viewContactMessageModal');
               if (!modal) return;
               modal.addEventListener('show.bs.modal', function (e) {
                    var btn = e.relatedTarget;
                    document.getElementById('cmViewName').textContent    = btn.dataset.name    || '—';
                    document.getElementById('cmViewEmail').textContent   = btn.dataset.email   || '—';
                    document.getElementById('cmViewPhone').textContent   = btn.dataset.phone   || '—';
                    document.getElementById('cmViewMessage').textContent = btn.dataset.message || '—';
                    document.getElementById('cmViewDate').textContent    = btn.dataset.date    || '—';
               });
          })();
          </script>

     <?php
     }
}
?>
