<?php

namespace DGZ_library\DGZ_views;

/**
 * Description of WarningListView
 *
 * @author Gustav Ndamukong
 */
class NoticeListView extends \DGZ_library\DGZ_View {
	
	/**
	 * Displays a list of notices as an unordered list
	 * @param array $notices An array of notices to display
	 */
	public function show(array $notices) {
	
		foreach($notices as $notice):
?>	
			<div class="alert alert-info">
<?php
				if(is_array($notice)):
?>
					<strong><?= $notice['title'] ?></strong>
					<p>
						<?= str_replace('<br />', '<p></p>', nl2br($notice['description'])) ?>
					</p>
<?php				
				else:
?>
					<p><?= str_replace('<br />', '<p></p>', nl2br($notice)) ?></p>
<?php
				endif;
?>
			</div>
<?php	
		endforeach;
	}
	
}




