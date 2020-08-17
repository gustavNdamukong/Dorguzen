<?php

namespace views\admin;



use controllers\FeedbackController;
use Testimonials;
use \DGZ_Pager;
use DGZ_library\DGZ_Dates;
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
							<li><a href="<?=$this->controller->settings->getFileRootPath()?>admin/adminHome">Dashboard</a></li>
							<li class="active">Manage Contact Messages</li>
						</ol>
					</div>
				</div>
			</div>
		</section>
		<!-- ==========================
            BREADCRUMB - END
        =========================== -->


		<!-- ==========================
            BLOG - START
        =========================== -->
		<section class="content blog">
		<div class="container">
		<h2 class="hidden">Manage Contact Messages</h2>
		<div class="row">
		<div class="col-sm-12">

		<!-- COMPONENTS:TABS - START -->
		<!--<h2 class="component-heading" id="tabs"><i class="fa fa-folder"></i>Manage Newsletters</h2>-->
		<div class="tabs">
		<?php /*<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class=""><a href="#demo-tabs-1" role="tab" data-toggle="tab"
												aria-controls="demo-tabs-1" aria-expanded="false">Send Welcome Letter</a></li>
			<li role="presentation"><a href="#demo-tabs-2" role="tab" data-toggle="tab"
													  aria-controls="demo-tabs-2" aria-expanded="true">Send Newsletter</a>
			</li>
			<li role="presentation" class="active"><a href="#demo-tabs-3" role="tab" data-toggle="tab"
												aria-controls="demo-tabs-3" aria-expanded="false">Create News Letters</a></li>
			<li><a class="btn btn-info" href="<?=$this->controller->settings->getFileRootPath()?>admin/adminHome">Return to Dashboard</a>
			</li>
		</ul>*/ ?>


		<div class="tab-content"><!--This wraps content of all tabs-->



				<?php ########################## TAB 3 ############################################# ?>

				<div role="tabpanel" class="tab-pane active in" id="demo-tabs-3">
		<!--	<p><h3>Manage Testimonials <button data-toggle="modal" class="btn btn-success btn-sm pull-right" data-target="#createNewsletterModal">Create New Letter</button></h3></p>-->
				<p><h3>Manage Contact Messages</h3></p>
				<?php
				$order = isset($_GET['ord'])?$_GET['ord']:'';
				$sort = isset($_GET['s'])?$_GET['s']:'';

				if ($contactMessages)
				{

				$pager = new DGZ_Pager($contactMessages);

				//add some extra columns
				//When you add an extra (custom) column to a table, u use addColumn(), and u pass it one param, the text to appear as the column heading text
				//Once an extra column is added, u will then have to add a matching number of column fields to go under this heading; do so using addFieldText() (to add text in the field)
				// or addFieldButton() (to add a btn in the field)
				$pager->addColumn('Action');

				//add field values for the 'Actions' column. We can create two buttons, but first let us create the link target for the field buttons
				//this specific link is not used as it is redirected by JS
				/////$approveLink = $this->controller->settings->getFileRootPath().'feedback/approveTestimonial?';
				$deleteLink = $this->controller->settings->getFileRootPath() . 'admin/deleteContactMessage?';

				//When adding a field button, you can pass six parameters to the addFieldButton() method of the DGZ_Pager class
				//-i) The column under which the button will appear
				//-ii) The type of button (the system currently accepts two types-'Edit' and 'Delete' buttons)
				//-iii) The value (text) to appear on the button
				//-iv) The link string where the click of the button will lead to
				//-v)  Optional array of parameters to be added as query string (URL) arguments to the link
				//-vi) Optional array of attributes to be built into the button element e.g. id, class, width, height etc
				//$pager->addFieldText('Actions', 'any text'); //works just fine
				/////$pager->addFieldButton('Actions', 'Edit', 'Manage', $approveLink, ['testimonials_id'], ['id' => 'openManageTestimonialModal', 'data-toggle' => 'modal', 'data-target' => '#editManageTestimonialModal']);
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

				/////$pager->makeClickable(true, $this->controller->settings->getFileRootPath().'feedback/testimonial?', ['testimonials_id']);

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




		</div> <!--END OF TABS (both the tab <ul> heading lists n their contents) col-m-12-->
			<br>
			<hr><br>


			<!-- COMPONENTS:TABS - END -->
			</div> <!--END OF ROW IN CONTAINER??-->


			<?php
			/*
			?>
			<div class="col-sm-3">
				<aside class="sidebar right">

				</aside>
			</div>
			*/ ?>
			<!--</div><!--END OF ROW HOLDING THE MAIN PAGE AND THE RIGHT SIDEBAR-->
			</div> <!--END OF THE CONTAINER HOLDING ALL-->
		</section><!--END OF SECTION HOLDING ALL, EVEN THE CONTAINER-->









		<!--------------------------------------
		EDIT NEWSLETTER MODAL FORM START
		--------------------------------------->
		<div class="modal fade" role="dialog" id="editManageTestimonialModal" style="display:none;">
			<div class="modal-dialog" style="background-color: #FFF; border-radius: 10px;">

				<!-- Modal content-->
				<div class="md-content">

					<!-- Modal header-->
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h3 style="background-color: #28a4c9; color: #FFF;font-weight: bold;" class="modal-title text-center">Approve/Suspend Testimonial</h3>
					</div>

					<!-- Modal body -->
					<div class="modal-body">
						<div>
							<p>Please fill in all fields:</p>
							<form id="editManageTestimonialForm" action="<?=$this->controller->settings->getFileRootPath()?>feedback/manageTestimonials?edit=1" method="post" enctype="multipart/form-data">

								<div class="form-group">
									<label for="tes_approve">Approve</label>
									<input class="form-control" type="radio" align="center" value="yes" id="tes_approve" name="tes_approve" />

									<label for="tes_suspend">Suspend</label>
									<input class="form-control" type="radio" align="center" value="suspended" id="tes_suspend" name="tes_approve" />
								</div>

									<input type="hidden" id="recId" name="recId" value="" />
									<input type="submit" class="btn btn-primary btn-sm" value="Submit"/>

							</form>
						</div>
					</div>

					<!-- Modal footer -->
					<div class="modal-footer">
						<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
					</div>
				</div>
			</div>
		</div>
		<!--------------------------------------
		EDIT NEWSLETTER MODAL FORM END
		--------------------------------------->
		
	<?php
	}
}
?>