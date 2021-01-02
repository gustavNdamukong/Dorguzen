<?php

namespace views\admin;

use \DGZ_Pager;


class manageContactMessages extends \DGZ_library\DGZ_HtmlView
{


	function show($contactMessages)
	{

		$this->addScript('general.js');

		?>
		<!-- ==========================
    	BREADCRUMB - START
		=========================== -->
		<section class="breadcrumb-wrapper">
			<div class="container">
				<div class="row">
					<div class="col-xs-6">
						<h3 class="text-center"><i class="fa fa-envelope-o"></i> Manage Contact Messages</h3>
					</div>
					<div class="col-xs-6">
						<ol class="breadcrumb">
							<li><a href="<?=$this->controller->settings->getFileRootPath()?>admin/dashboard">Dashboard</a></li>
							<li class="active">Manage Contact Messages</li>
						</ol>
					</div>
				</div>
			</div>
		</section>
		<!-- ==========================
            BREADCRUMB - END
        =========================== -->

		<section class="content blog">
			<div class="container">
				<h2 class="hidden">Manage Contact Messages</h2>
				<div class="row">
					<div class="col-sm-12">
						<div class="tabs">
							<div class="tab-content">
								<div role="tabpanel" class="tab-pane active in" id="demo-tabs-3">
									<p><h3>Manage Contact Messages</h3></p>
									<?php
									$order = isset($_GET['ord'])?$_GET['ord']:'';
									$sort = isset($_GET['s'])?$_GET['s']:'';

									if ($contactMessages)
									{

									$pager = new DGZ_Pager($contactMessages);

									//add some extra columns
									$pager->addColumn('Action');

									$deleteLink = $this->controller->settings->getFileRootPath() . 'admin/deleteContactMessage?';

									$pager->addFieldButton('Action', 'Delete', 'Delete', $deleteLink, ['contactformmessage_id'], ['id' => 'deleteContactMessageBtn', 'class' => 'btn btn-danger']);
									$pager->makeSortable(true);

									/**
									 * Specify the target back link for the sort links which will be passed to getTable() - do this obviously only if you have set 'makeSortable()' to true
									 * Note very carefully that if you have specified '$pager->makeSortable' as true and set the $sortLinkTarget variable, then you MUST call getTable() below like so:
									 *        $table = $pager->getTable('blog_posts_TableView', $sortLinkTarget, $limit, $page);
									 * else you MUST call it like so:
									 *        $table = $pager->getTable('newsletter_TableView', '', $limit, $page); (leaving the 2nd argument meant for the sort links blank)
									 * for your links and pagination functionality to work well
									 */
									$sortLinkTarget = $this->controller->settings->getFileRootPath() . 'admin/contactMessages?';

									//set pagination vars
									$limit = (isset($_GET['limit'])) ? $_GET['limit'] : 10;
									$page = (isset($_GET['pageNum'])) ? $_GET['pageNum'] : 1;
									$links = (isset($_GET['links'])) ? $_GET['links'] : 3;

									$table = $pager->getTable('contactMessages_TableView', $sortLinkTarget, $limit, $page); ?>

									<?= $table ?>

									<?php
									$linkTarget = $this->controller->settings->getFileRootPath() . 'admin/contactMessages?ord=' . $order . '&s=' . $sort;
									echo $pager->createLinks($links, $linkTarget, 'pagination pagination-xs'); ?>

								</div>
							<?php
							}
							else
							{ ?>
								<h4>There are no contact messages yet</h4>
							<?php
							} ?>
							</div>
						</div> <!--End of div that wraps the content of all tabs-->

					</div>
					<br>
					<hr>
				</div>
			</div> 
		</section>
		
	<?php
	}
}
?>