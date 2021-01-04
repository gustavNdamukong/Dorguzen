<?php

namespace DGZ_library\DGZ_views;


class SuccessListView extends \DGZ_library\DGZ_View {
	
	/**
	 * Displays a list of success messages as an unordered list
	 * @param array $successes An array of success messages to display
	 *
	 * @author Gustav Ndamukong
	 */
	public function show(array $successes) {
	
		foreach($successes as $success):
?>	
			<div class="alert alert-success">
<?php
				if(is_array($success)):
?>	
					<strong><?= $success['title'] ?></strong>
					<p>
						<?= str_replace('<br />', '<p></p>', nl2br($success['description'])) ?>
					</p>
<?php				
				else:
?>
					<p><?= str_replace('<br />', '<p></p>', nl2br($success)) ?></p>
<?php
				endif;
?>
			</div>
<?php	
		endforeach;
	}
	
}