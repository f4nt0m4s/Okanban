<?php
namespace src\controller;

use src\model\Card;
use src\model\CardDAO;

class CardController {

	private $cardDAO;

	public function __construct() {
		$this->cardDAO = new CardDAO();
	}

	/**
	 * @param string $title : le titre de la colonne
	 * @param int $kanban_id : l'idenfiant du kanban
	 * @return Card|null : la colonne indiqué par son titre et le kanban au quel elle appartient
	 */
	public function getCardByTitleKanbanId(string $title, int $kanban_id) : ?Card {
		return $this->cardDAO->findByTitleKanbanId($title, $kanban_id);
	}

	// les colonnes associés à un kanban
	/**
	 * @param int $kanban_id : l'idenfiant du kanban
	 * @return array : liste des colonnes pour un kanban donné
	 */
	public function getCardsByKanbanId(int $kanban_id) : array {
		$cards = $this->cardDAO->findAllByKanbanId($kanban_id);
		return is_null($cards) || empty($cards) ? array() : $cards;
	}

	/**
	 * @param Card $card : la colonne a insérée
	 * @return Card|null : Effectue l'insertion et récupère l'objet Task inséré
	 */
	public function createCard(Card $card) : ?Card {
		if (is_null($card)) { return null; }
		if (!$this->cardDAO->insert($card)) {
			return null;
		}
		$cardId = $this->cardDAO->lastInsertId();
		if (is_null($cardId)) {
			// delete($card)
			return null;
		}
		return $this->cardDAO->findById($cardId);
	}

}

?>