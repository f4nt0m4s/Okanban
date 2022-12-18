<?php
namespace src\controller;

use src\model\Task;
use src\model\User;
use src\model\TaskUser;
use src\model\TaskUserDAO;

class TaskUserController {

	private $taskUserDAO;
	public function __construct() {
		$this->taskUserDAO = new TaskUserDAO();
	}

	/**
	 * @param Task|null $task : la tâche pour l'utilisateur
	 * @param User|null $user : l'utilisateur qui doit effectuer la tâche
	 * @return bool : true si l'utilisateur a été ajouté à cette tâche, false sinon
	 */
	public function addUserToTask(Task $task = null, User $user = null) : bool {
		if (is_null($task) || is_null($user)) { return false; }
		$taskUser = new TaskUser($task->getId(), $user->getId());
		if (!$this->taskUserDAO->insert($taskUser)) {
			return false;
		}
		return true;
	}
}

?>