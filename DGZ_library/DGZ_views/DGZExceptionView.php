<?php

namespace DGZ_library\DGZ_views;


use settings\Settings;

/**
 * Displays a DGZ_library/Exception in HTML format
 *
 * @author Gustav
 */

class DGZExceptionView  extends \DGZ_library\DGZ_View {



	public function show(\DGZ_library\DGZ_Exception $e) {
		$settings = new Settings();
		?>
	<div class="container">
		<h3 class="animated bounceInDown">Oops <strong>something</strong> went wrong</h3>
		<h4 class="animated bounceInUp skincolored">find <strong>below</strong> our hints to the possible issue!</h4>
		<section class="hgroup">
			<div class="container col-xs-12 col-md-9">
				<h1>DGZException</h1>
				<ul class="breadcrumb -align-center">
					<li><a href="<?=$settings->getHomePage()?>">Home</a></li>
					<li class="active">DGZExceptionpage</li>
				</ul>
			</div>
		</section>
		<section class="article-text">
			<div class="main">
				<div class="row">

					<div class="alert alert-danger col-xs-6 col-md-9">
						<strong>Error: <?= nl2br(htmlspecialchars($e->getMessage())) ?></strong>

					<?php
						if($e->getHint()):
					?>
							<p><?= str_replace('<br />', '</p><p>', nl2br(htmlspecialchars($e->getHint())))?></p>
					<?php
						endif;

					//check if are not live, n only if so, throw exception details on screen
					if($settings->getSettings()['live'] == false)
					{ ?>
							<hr/>
							<div class="technical-info">
								<p><strong>Technical Information - Not shown on Live</strong></p>
								<p class="exception-source"><?= get_class($e) ?> thrown in file: <?= $e->getFile() ?> on line <?= $e->getLine() ?></p>
								<p>Stack Trace:</p>
								<pre>
					<?= htmlspecialchars(trim($e->getTraceAsString())) ?>
								</pre>
							</div>
						<?php
					} ?>

					</div>

				</div>
			</div>
		</section>

	</div>

	<?php
	
	}
	
	
}
