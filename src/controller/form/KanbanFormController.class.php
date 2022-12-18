<?php
namespace src\controller\form;

class KanbanFormController {

	private static $keySort = [
		'titleAscendant' => 'titleAscendant',
		'titleDescendant' => 'titleDescendant',
		'dateAscendant' => 'dateAscendant',
		'dateDescendant' => 'dateDescendant'
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

	public static function getKeySortByDateAscendant() : string {
		return self::$keySort['dateAscendant'];
	}

	public static function getKeySortByDateDescendant() : string {
		return self::$keySort['dateDescendant'];
	}
}
?>