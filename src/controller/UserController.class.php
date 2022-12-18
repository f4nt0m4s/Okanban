<?php
namespace src\controller;

use src\model\User;
use src\model\UserDAO;

class UserController {

	private $userDAO;

	public function __construct() {
		$this->userDAO = new UserDAO();
	}

	/**
	 * @param string $username : le nom d'utilisateur
	 * @return User|null : l'utilisateur ayant ce nom d'utilisateur
	 */
	public function getUserByUsername(string $username = '') : ?User {
		return $this->userDAO->findByUsername($username);
	}

	/**
	 * @param int $kanban_id : l'identifiant du kanban
	 * @return array : la liste des utilisateurs participants à un kanban
	 */
	public function getUsersByKanbanId(int $kanban_id = -1) : array {
		return $this->userDAO->findAllByKanbanId($kanban_id);
	}

	/**
	 * @return array : la liste des utilisateurs affectés à une tâche
	*/
	public function getUsersByTaskId(int $task_id = -1) : array {
		return $this->userDAO->findAllByTaskId($task_id);
	}

}

?>