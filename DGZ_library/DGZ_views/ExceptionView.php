<?php

namespace DGZ_library\DGZ_views;

/**
 * Description of ExceptionView
 *
 * @author Gustav
 */
class ExceptionView extends \DGZ_library\DGZ_View {
	
	public function show(\Exception $e) {
		
?>

		<div class="alert alert-danger col-xs-6 col-md-9">
			<strong>Exception</strong>

			<?= nl2br(htmlspecialchars($e->getMessage())) ?>

			<?php if($e->getTraceAsString()): ?>
				<p>
					<pre>
		<?= nl2br(htmlspecialchars($e->getTraceAsString())) ?>
					</pre>
				</p>
			<?php endif; ?>

		</div>

<?php
		
		
	}
	
	
	
	
}
