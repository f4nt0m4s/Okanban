<?php
namespace src\controller;

use src\model\Kanban;
use src\model\User;
use src\model\KanbanUser;
use src\model\KanbanUserDAO;

class KanbanUserController {

	private $kanbanUserDAO;
	public function __construct() {
		$this->kanbanUserDAO = new KanbanUserDAO();
	}

	/**
	 * @param Kanban|null $kanban
	 * @param User|null $user
	 * @return KanbanUser|null
	 */
	public function getKanbanUser(Kanban $kanban = null, User $user = null) : ?KanbanUser {
		if (is_null($kanban) || is_null($user)) { return null; }
		return $this->kanbanUserDAO->find(new KanbanUser($kanban->getId(), $user->getId()));
	}

	/**
	 * @param Kanban|null $kanban
	 * @param User|null $user
	 * @return bool : true si l'utilisateur a été ajouté au kanban, false sinon
	 */
	public function addUserToKanban(Kanban $kanban = null, User $user = null) : bool {
		if (is_null($kanban) || is_null($user)) { return false; }
		$kanbanUser = new KanbanUser($kanban->getId(), $user->getId());
		if (!$this->kanbanUserDAO->insert($kanbanUser)) {
			return false;
		}
		return true;
	}

}

?>