<?php

namespace DGZ_library\DGZ_views;

/**
 * Description of ErrorsListView
 *
 * @author Gustav Ndamukong
 */
class ErrorsListView extends \DGZ_library\DGZ_View {
	
	/**
	 * Displays a list of errors as an unordered list
	 * @param array $errors
	 */
	public function show(array $errors) {
	
		foreach($errors as $error):
?>	
			<div class="alert alert-danger">
<?php
				if(is_array($error)):
?>
					<strong><?= $error['title'] ?></strong>
					<p>
						<?= str_replace('<br />', '<p></p>', nl2br($error['description'])) ?>
					</p>
<?php				
				else:
?>
					<p><?= str_replace('<br />', '<p></p>', nl2br($error)) ?></p>
<?php
				endif;
?>
			</div>
<?php	
		endforeach;
	}
	
}
