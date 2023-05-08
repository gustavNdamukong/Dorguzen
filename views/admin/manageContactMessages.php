<?php

namespace views\admin;

use DGZ_library\DGZ_Table;


class manageContactMessages extends \DGZ_library\DGZ_HtmlView
{


	function show($contactMessages)
	{

		$this->addScript('general.js');

		?>
		<!-- ==========================
    	BREADCRUMB - START
		=========================== -->
		<!-- Hero Header Start -->
		<div class="container-xxl py-5 bg-primary hero-header mb-5">
			<div class="container my-5 py-5 px-lg-5">
				<div class="row g-5 py-5">
					<div class="col-12 text-center">
						<h1 class="text-white animated zoomIn">Contact Messages</h1>
						<hr class="bg-white mx-auto mt-0" style="width: 90px;">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb justify-content-center">
								<li class="breadcrumb-item"><a class="text-white" href="<?= $this->controller->settings->getFileRootPath() ?>admin/dashboard">Dashboard</a></li>
								<li class="breadcrumb-item text-white active" aria-current="page">Contact Messages</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
		</div>
		</div>
		<!-- Hero Header End -->
		<!-- ==========================
            BREADCRUMB - END
        =========================== -->

		<section class="content blog">
			<div class="container">
				<div class="row">
					<div class="col-sm-12">
						<div class="tabs">
							<div class="tab-content">
								<div role="tabpanel" class="tab-pane active in" id="demo-tabs-3">
									<p><h3>Manage Contact Messages</h3></p>
									<?php
									if ($contactMessages)
									{

									$pager = new DGZ_Table($contactMessages);

									//add some extra columns
									$pager->addColumn('Action');

									$deleteLink = $this->rootPath() . 'admin/deleteContactMessage';

									$pager->addFieldButton(
										'Action',
										'Delete',
										'Delete',
										$deleteLink,
										['contactformmessage_id'],
										['id' => 'deleteContactMessageBtn',
											'class' => 'btn btn-danger']
									);
									$pager->makeSortable(true);
									$dataLinkPath = $this->rootPath() . 'admin/contactMessages';

									//set pagination max num of records per page
									$pager->setRecordsPerpage(10);

									$table = $pager->getTable('contactMessages_TableView', $dataLinkPath); ?>

									<?= $table ?>

									<?php
									echo $pager->createLinks($dataLinkPath, 'pagination'); ?>
									<?php
									}
									else
									{ ?>
										<h4>There are no contact messages yet</h4>
									<?php
									} ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div> 
		</section>
		
	<?php
	}
}
?>