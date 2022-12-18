<?php
namespace src\controller\form;

class KanbanTasksFormController {

	private static $keySort = [
		'titleAscendant' => 'titleAscendant',
		'titleDescendant' => 'titleDescendant',
		'createdAtAscendant' => 'createdAtAscendant',
		'createdAtDescendant' => 'createdAtDescendant',
		'updatedAtAscendant' => 'updatedAtAscendant',
		'updatedAtDescendant' => 'updatedAtDescendant',
		'descriptionAscendant' => 'descriptionAscendant',
		'descriptionDescendant' => 'descriptionDescendant',
		'limitDateAscendant' => 'limitDateAscendant',
		'limitDateDescendant' => 'limitDateDescendant'
	];

	private function __construct() {}

	public static function getKeySort() : array {
		return self::$keySort;
	}

	public static function getKeySortByTitleAscendant() : string {
		return self::$keySort['titleAscendant'];
	}
	
	public static function getKeySortByTitleDescendant() : string {
		return self::$keySort['titleDescendant'];
	}

	public static function getKeySortByCreatedAtAscendant() : string {
		return self::$keySort['createdAtAscendant'];
	}

	public static function getKeySortByCreatedAtDescendant() : string {
		return self::$keySort['createdAtDescendant'];
	}

	public static function getKeySortByUpdatedAtAscendant() : string {
		return self::$keySort['updatedAtAscendant'];
	}

	public static function getKeySortByUpdatedAtDescendant() : string {
		return self::$keySort['updatedAtDescendant'];
	}

	public static function getKeySortByDescriptionAscendant() : string {
		return self::$keySort['descriptionAscendant'];
	}

	public static function getKeySortByDescriptionDescendant() : string {
		return self::$keySort['descriptionDescendant'];
	}

	public static function getKeySortByLimitDateAscendant() : string {
		return self::$keySort['limitDateAscendant'];
	}

	public static function getKeySortByLimitDateDescendant() : string {
		return self::$keySort['limitDateDescendant'];
	}
}
?>