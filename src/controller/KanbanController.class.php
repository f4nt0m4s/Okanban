<?php
namespace src\controller;

use src\Session;
use src\model\Kanban;
use src\model\KanbanDAO;
use src\model\KanbanUser;
use src\model\KanbanUserDAO;
use src\model\User;
use src\model\UserDAO;
use src\model\Card;
use src\model\CardDAO;

class KanbanController {

	private static $defaultNbColumns = 2;

	private $kanbanDAO;

	public function __construct() {
		$this->kanbanDAO = new KanbanDAO();
	}

	/**
	 * @return int : le nombre de colonnes par défault pour un kanban
	 */
	public static function getDefaultNbColumns() {
		return self::$defaultNbColumns;
	}

	/**
	 * @return array : tableau des visibilités pour un kanban
	 */
	public static function getAllVisibility() : array {
		return Kanban::getAllVisibility();
	}

	/**
	 * @return array : la liste des kanbans ayant la visiblité publique
	 */
	public function getPublicKanbans() : array {
		$kanbans = $this->kanbanDAO->findByVisibility(Kanban::getVisibilityPublic());
		return is_null($kanbans) || empty($kanbans) ? array() : $kanbans;
	}

	/**
	 * @return array : la liste des kanbans de l'utilisateur de la session
	 */
	public function getMyKanbans() : array {
		Session::start();
		$userSession = unserialize($_SESSION[Session::getUserIndex()]);
		if (is_null($userSession) || !$userSession) {
			return array();
		}
		$kanbans = $this->kanbanDAO->findAllByCreatorId($userSession->getId());
		return is_null($kanbans) || empty($kanbans) ? array() : $kanbans;
	}
	
	/**
	 * @param string $columnName : le nom de la colonne de la table kanban
	 * @param string $order : ASC/DESC
	 * @return array : la liste des kanbans de l'utilisateur de la session selon l'ordre définit de la colonne
	 */
	public function getMyKanbansOrdered(string $columnName = '', string $order = 'ASC') : array {
		Session::start();
		$userSession = unserialize($_SESSION[Session::getUserIndex()]);
		if (is_null($userSession) || !$userSession) {
			return array();
		}
		$kanbans = $this->kanbanDAO->findAllByCreatorIdOrderByColumnName($userSession->getId(), $columnName, $order);
		return is_null($kanbans) || empty($kanbans) ? array() : $kanbans;
	}
	
	public function getMyKanbansParticipations() : array {
		Session::start();
		$userSession = unserialize($_SESSION[Session::getUserIndex()]);
		if (is_null($userSession) || !$userSession) {
			return array();
		}
		$kanbanUserDAO = new KanbanUserDAO();
		$kanbansUser = $kanbanUserDAO->findAllByUserId($userSession->getId());
		$ids = array();
		foreach ($kanbansUser as $kanbanUser) {
			array_push($ids, $kanbanUser->getKanbanId());
		}
		$kanbans = $this->kanbanDAO->findAllByIds($ids);
		return is_null($kanbans) || empty($kanbans) ? array() : $kanbans;
	}

	public function getMyKanbansParticipationsOrdered(string $columnName = '', string $order = 'ASC') : array {
		Session::start();
		$userSession = unserialize($_SESSION[Session::getUserIndex()]);
		if (is_null($userSession) || !$userSession) {
			return array();
		}
		$kanbanUserDAO = new KanbanUserDAO();
		$kanbansUser = $kanbanUserDAO->findAllByUserId($userSession->getId());
		$ids = array();
		foreach ($kanbansUser as $kanbanUser) {
			array_push($ids, $kanbanUser->getKanbanId());
		}
		$kanbans = $this->kanbanDAO->findAllByIdsOrderByColumnName($ids, $columnName, $order);
		return is_null($kanbans) || empty($kanbans) ? array() : $kanbans;
	} 

	/**
	 * @param string $title : le titre du kanban
	 * @return Kanban|null : le kanban identifié par son titre
	 */
	public function getKanbanByTitle(string $title) : ?Kanban {
		return $this->kanbanDAO->findByTitle($title);
	}

	/**
	 * @param string $slug : le label du kanban
	 * @return Kanban|null : le kanban identifié par son label
	 */
	public function getKanbanBySlug(string $slug) : ?Kanban {
		return $this->kanbanDAO->findBySlug($slug);
	}

	/**
	 * @param string $title : le titre du kanban
	 * @return Kanban|null : le kanban indiqué par le titre pour l'utilisateur de la session
	 */
	public function getMyKanbanByTitle(string $title) : ?Kanban {
		if (is_null($title)) { return null; }
		Session::start();
		$userSession = unserialize($_SESSION[Session::getUserIndex()]);
		if (is_null($userSession) || !$userSession) {
			return null;
		}
		return $this->kanbanDAO->findByTitleCreatorId($title, $userSession->getId());
	} 

	/**
	 * @param int $creator_id : l'identifiant de l'utilisateur qui a crée le kanban
	 * @return User|null : l'utilisateur qui a crée le kanban
	 */
	public function getUserByCreatorId(int $creator_id) : ?User {
		$userDAO = new UserDAO();
		return $userDAO->findById($creator_id);
	}

	/**
	 * @param Kanban $kanban : le kanban à insérer
	 * @return bool : true si le kanban a été inséré, false sinon
	 */
	public function createKanban(Kanban $kanban) : bool {
		if (is_null($kanban)) { return false; }

		// Vérification qu'il n'existe pas un kanban du même titre pour ce même auteur
		$containKanban = $this->kanbanDAO->findByTitleCreatorId($kanban->getTitle(), $kanban->getCreatorId());
		if (!is_null($containKanban)) {
			return false;
		}
		
		// Insertion du kanban en base de données
		// Insertion dans la table kanban
		if (!$this->kanbanDAO->insert($kanban)) {
			return false;
		}
		
		// Récupère le kanban insérée (car id a changé)
		$kanbanInserted = $this->kanbanDAO->findByTitleCreatorId($kanban->getTitle(), $kanban->getCreatorId());
		if (is_null($kanbanInserted)) {
			return false;
		}

		// Insertion dans la table d'association
		$kanbanUserDAO = new KanbanUserDAO();
		$kanbanUser = new KanbanUser($kanbanInserted->getId(), $kanbanInserted->getCreatorId());
		if (!$kanbanUserDAO->insert($kanbanUser)) {
			$this->kanbanDAO->delete($kanbanInserted);
			return false;
		}

		// Cards(colonnes) par défaut
		$date = date("Y-m-d H:i:s");
		$cards = [
			new Card(-1, $date, 'Stories', $kanbanInserted->getId()), 
			new Card(-1, $date, 'Done', $kanbanInserted->getId()),
		];

		$cardDAO = new CardDAO();
		foreach ($cards as $card) {
			// Insertion de la colonne (card) en base de données
			if (!$cardDAO->insert($card)) {
				// Delete en cas d'erreur d'insertion(s)
				$kanbanUserDAO->delete($kanbanUser);
				$this->kanbanDAO->delete($kanbanInserted);
				foreach ($cards as $card) {
					$cardDAO->delete($card);
				}
				return false;
			}
		}
		return true;
	}

}

?>