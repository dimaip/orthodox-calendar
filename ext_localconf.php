<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Orthodox',
	array(
		'Day' => 'list',
		'Bible' => 'list',
	),
	// non-cacheable actions
	array(	
		'Day' => 'list',
		'Bible' => 'list',		
	)
);

?>