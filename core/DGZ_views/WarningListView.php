<?php

namespace Dorguzen\Core\DGZ_views;


use Dorguzen\Core\DGZ_View;



/**
 * Description of WarningListView
 *
 * @author Gustav Ndamukong
 */
class WarningListView extends DGZ_View {
	
	/**
	 * Displays a list of warnings as an unordered list
	 * @param array $warnings
	 */
	public function show(array $warnings) {
	
		foreach($warnings as $warning):
?>	
			<div class="alert alert-warning">
<?php
				if(is_array($warning)):
?>
					<strong><?= $warning['title'] ?></strong>
					<p>
						<?= str_replace('<br />', '<p></p>', nl2br($warning['description'])) ?>
					</p>
<?php				
				else:
?>
					<p><?= str_replace('<br />', '<p></p>', nl2br($warning)) ?></p>
<?php
				endif;
?>
			</div>
<?php	
		endforeach;
	}
	
}


