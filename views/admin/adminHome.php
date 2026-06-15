<?php

namespace Dorguzen\Views\Admin;

class adminHome extends \Dorguzen\Core\DGZ_AdminHtmlView
{

function show(array $viewModel = [])
{
     ?>

     <!-- Hero Header Start -->
     <div class="container-xxl py-5 bg-primary hero-header mb-5">
          <div class="container my-5 py-5 px-lg-5">
               <div class="row g-5 py-5">
                    <div class="col-12 text-center">
                         <h1 class="text-white animated zoomIn">Dashboard</h1>
                         <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                         <nav aria-label="breadcrumb">
                              <ol class="breadcrumb justify-content-center">
                                   <li class="breadcrumb-item"><a class="text-white" href="<?= $this->controller->config->getFileRootPath() ?>">Home</a></li>
                                   <li class="breadcrumb-item text-white active" aria-current="page">Dashboard</li>
                              </ol>
                         </nav>
                    </div>
               </div>
          </div>
     </div>
     <!-- Hero Header End -->

     <style>
          .dgz-card-grid {
               display: grid;
               grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
               gap: 18px;
               margin-bottom: 30px;
          }
          .dgz-dash-card {
               border-radius: 16px;
               padding: 24px 12px 20px;
               text-align: center;
               text-decoration: none;
               color: #fff;
               display: flex;
               flex-direction: column;
               align-items: center;
               gap: 10px;
               transition: transform .15s ease, box-shadow .15s ease;
               box-shadow: 0 4px 14px rgba(0,0,0,.13);
               cursor: pointer;
          }
          .dgz-dash-card:hover {
               transform: translateY(-4px);
               box-shadow: 0 8px 24px rgba(0,0,0,.2);
               color: #fff;
               text-decoration: none;
          }
          .dgz-dash-card .dgz-icon {
               font-size: 2.4rem;
               line-height: 1;
          }
          .dgz-dash-card .dgz-label {
               font-size: .88rem;
               font-weight: 600;
               letter-spacing: .3px;
          }
          .dgz-section-title {
               font-size: 1rem;
               font-weight: 700;
               text-transform: uppercase;
               letter-spacing: 1px;
               color: #6c757d;
               margin: 28px 0 14px;
               border-bottom: 2px solid #f0f0f0;
               padding-bottom: 6px;
          }
          /* card colour palette */
          .dgz-c-violet   { background: linear-gradient(135deg, #6f42c1, #9b59b6); }
          .dgz-c-blue     { background: linear-gradient(135deg, #0d6efd, #3a9bd5); }
          .dgz-c-teal     { background: linear-gradient(135deg, #0d9488, #14b8a6); }
          .dgz-c-green    { background: linear-gradient(135deg, #198754, #20c997); }
          .dgz-c-orange   { background: linear-gradient(135deg, #fd7e14, #ffc107); }
          .dgz-c-pink     { background: linear-gradient(135deg, #d63384, #f06292); }
          .dgz-c-red      { background: linear-gradient(135deg, #dc3545, #e57368); }
          .dgz-c-indigo   { background: linear-gradient(135deg, #3949ab, #5c6bc0); }
          .dgz-c-cyan     { background: linear-gradient(135deg, #0dcaf0, #0ea5e9); }
          .dgz-c-brown    { background: linear-gradient(135deg, #795548, #a1887f); }
          .dgz-c-slate    { background: linear-gradient(135deg, #475569, #64748b); }
          .dgz-c-lime     { background: linear-gradient(135deg, #65a30d, #a3e635); }
          /* disabled/coming-soon cards */
          .dgz-dash-card.dgz-disabled {
               opacity: .55;
               cursor: not-allowed;
               pointer-events: none;
          }
          /* sidebar quick links */
          .dgz-qlink {
               display: flex;
               align-items: center;
               gap: 8px;
               padding: 10px 12px;
               border-radius: 10px;
               text-decoration: none;
               color: #374151;
               font-size: .9rem;
               font-weight: 500;
               transition: background .15s ease, color .15s ease;
          }
          .dgz-qlink:hover {
               background: #f3f4f6;
               color: #111;
               text-decoration: none;
          }
          .dgz-qlink-danger { color: #dc3545; }
          .dgz-qlink-danger:hover { background: #fff0f0; color: #b91c1c; }
     </style>

     <!-- ==========================
          PAGE CONTENT - START
     =========================== -->
     <div class="main">
          <section class="content account bg-white">
               <div class="container">
                    <div class="row">
                         <div class="col-sm-3 mb-4">
                              <aside class="sidebar">
                                   <!-- WIDGET:SIDEBAR - START -->
                                   <div class="card border-0 shadow-sm" style="border-radius:16px;overflow:hidden;">
                                        <div class="card-body p-0">
                                             <div class="bg-primary px-3 py-3">
                                                  <h6 class="text-white mb-0 fw-bold" style="letter-spacing:.5px;">⚡ Quick Links</h6>
                                             </div>
                                             <div class="d-flex flex-column p-2 gap-1">
                                                  <a href="<?=$this->controller->config->getFileRootPath()?>admin/dashboard"
                                                       class="dgz-qlink">
                                                       🏠 <span>Dashboard</span>
                                                  </a>
                                                  <a href="<?=$this->controller->config->getFileRootPath()?>"
                                                       class="dgz-qlink">
                                                       🌐 <span>View Site</span>
                                                  </a>
                                                  <a href="<?=$this->controller->config->getFileRootPath()?>admin/logout"
                                                       class="dgz-qlink dgz-qlink-danger">
                                                       🚪 <span>Logout</span>
                                                  </a>
                                             </div>
                                        </div>
                                   </div>
                                   <!-- WIDGET:SIDEBAR - END -->
                              </aside>
                         </div>

                         <div class="col-sm-9">
                              <article class="account-content">

                                   <!-- START SIDE SLIDE-IN MENU -->
                                   <?php
                                   $jsValidation = \Dorguzen\Core\DGZ_View::getInsideView('sideSlideInMenuPartial', $this->controller);
                                   $jsValidation->show();
                                   ?>
                                   <!-- END OF SIDE SLIDE-IN MENU -->

                                   <h3>Welcome <span><?= $_SESSION['first_name'] ?></span> 👋</h3>
                                   <p class="text-muted">Manage your site from here — content, users, settings, and more.</p>

                                   <!-- ── MY ACCOUNT ── -->
                                   <p class="dgz-section-title">My Account</p>
                                   <div class="dgz-card-grid">

                                        <a href="<?=$this->controller->config->getFileRootPath()?>admin/adminUserChangePw?userId=<?=$_SESSION['custo_id']?>&change=0"
                                             class="dgz-dash-card dgz-c-slate">
                                             <span class="dgz-icon">🔐</span>
                                             <span class="dgz-label">Change Password</span>
                                        </a>

                                   </div>

                                   <?php if (in_array($_SESSION['user_type'] ?? '', ['admin', 'admin_gen', 'super_admin'])) { ?>

                                   <!-- ── CONTENT ── -->
                                   <p class="dgz-section-title">Content</p>
                                   <div class="dgz-card-grid">

                                        <a href="<?= $this->controller->config->getFileRootPath() ?>admin/news" class="dgz-dash-card dgz-c-orange">
                                             <span class="dgz-icon">📰</span>
                                             <span class="dgz-label">News</span>
                                        </a>

                                        <a href="<?= $this->controller->config->getFileRootPath() ?>admin/portfolio"
                                             class="dgz-dash-card dgz-c-lime">
                                             <span class="dgz-icon">🖼️</span>
                                             <span class="dgz-label">Portfolio</span>
                                        </a>

                                   </div>

                                   <!-- ── COMMUNICATIONS ── -->
                                   <p class="dgz-section-title">Communications</p>
                                   <div class="dgz-card-grid">

                                        <a href="<?=$this->controller->config->getFileRootPath()?>admin/contactMessages"
                                             class="dgz-dash-card dgz-c-blue">
                                             <span class="dgz-icon">✉️</span>
                                             <span class="dgz-label">Messages</span>
                                        </a>

                                        <a href="<?=$this->controller->config->getFileRootPath()?>admin/newsletters"
                                             class="dgz-dash-card dgz-c-teal">
                                             <span class="dgz-icon">📧</span>
                                             <span class="dgz-label">Newsletters</span>
                                        </a>

                                        <a href="<?=$this->controller->config->getFileRootPath()?>admin/subscribers"
                                             class="dgz-dash-card dgz-c-green">
                                             <span class="dgz-icon">👥</span>
                                             <span class="dgz-label">Subscribers</span>
                                        </a>

                                        <a href="<?=$this->controller->config->getFileRootPath()?>feedback/manageTestimonials"
                                             class="dgz-dash-card dgz-c-green">
                                             <span class="dgz-icon">⭐</span>
                                             <span class="dgz-label">Testimonials</span>
                                        </a>

                                   </div>

                                   <!-- ── ADMINISTRATION ── -->
                                   <p class="dgz-section-title">Administration</p>
                                   <div class="dgz-card-grid">

                                        <a href="<?=$this->controller->config->getFileRootPath()?>admin/manageUsers"
                                             class="dgz-dash-card dgz-c-indigo">
                                             <span class="dgz-icon">👥</span>
                                             <span class="dgz-label">Manage Users</span>
                                        </a>

                                        <a href="<?=$this->controller->config->getFileRootPath()?>admin/baseSettings?change=0"
                                             class="dgz-dash-card dgz-c-violet">
                                             <span class="dgz-icon">⚙️</span>
                                             <span class="dgz-label">Settings</span>
                                        </a>

                                        <a href="<?=$this->controller->config->getFileRootPath()?>admin/log"
                                             class="dgz-dash-card dgz-c-pink">
                                             <span class="dgz-icon">📋</span>
                                             <span class="dgz-label">System Logs</span>
                                        </a>

                                   </div>

                                   <!-- ── MODULES ── -->
                                   <p class="dgz-section-title">Modules</p>
                                   <div class="dgz-card-grid">

                                        <a href="<?=$this->controller->config->getFileRootPath()?>seo"
                                             class="dgz-dash-card dgz-c-cyan">
                                             <span class="dgz-icon">⚡</span>
                                             <span class="dgz-label">Typhoon SEO</span>
                                        </a>

                                        <?php if (config('app.modules.gallery') === 'on'): ?>
                                        <a href="<?=$this->controller->config->getFileRootPath()?>admin/gallery"
                                             class="dgz-dash-card dgz-c-orange">
                                             <span class="dgz-icon">🖼️</span>
                                             <span class="dgz-label">Gallery</span>
                                        </a>
                                        <?php endif; ?>

                                        <?php if (config('app.modules.videos') === 'on'): ?>
                                        <a href="<?=$this->controller->config->getFileRootPath()?>admin/videos"
                                             class="dgz-dash-card dgz-c-dark">
                                             <span class="dgz-icon">🎬</span>
                                             <span class="dgz-label">Videos</span>
                                        </a>
                                        <?php endif; ?>

                                        <?php if (config('app.modules.blog') === 'on'): ?>
                                        <a href="<?= $this->controller->config->getFileRootPath() ?>admin/blog"
                                             class="dgz-dash-card dgz-c-brown">
                                             <span class="dgz-icon">✍️</span>
                                             <span class="dgz-label">Blog</span>
                                        </a>
                                        <?php endif; ?>

                                        <a href="<?=$this->controller->config->getFileRootPath()?>payments"
                                             class="dgz-dash-card dgz-c-green dgz-disabled">
                                             <span class="dgz-icon">💳</span>
                                             <span class="dgz-label">Payments</span>
                                        </a>

                                   </div>

                                   <?php } ?>

                              </article>
                         </div>
                    </div>
               </div>
          </section>
     </div>
     <!-- ==========================
          PAGE CONTENT - END
     =========================== -->
     <?php
     }
} ?>
