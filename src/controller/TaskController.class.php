<?php
namespace src\controller;

use src\Session;
use src\model\Task;
use src\model\TaskDAO;
use src\model\TaskUserDAO;

class TaskController {

	private $taskDAO;

	public function __construct() {
		$this->taskDAO = new TaskDAO();
	}
	
	/**
	 * @return array : les tâches de l'utilisateur de la session
	*/
	public function getMyTasks() : array {
		Session::start();
		$userSession = unserialize($_SESSION[Session::getUserIndex()]);
		if (is_null($userSession) || !$userSession) {
			return array();
		}
		$taskUserDAO = new TaskUserDAO();
		$taskUsers = $taskUserDAO->findAllByUserId($userSession->getId());
		$tasks = array();
		foreach ($taskUsers as $taskUser) {
			$task = $this->taskDAO->findById($taskUser->getTaskId());
			if (!is_null($task)) {
				array_push($tasks, $task);
			}
		}
		return is_null($tasks) || empty($tasks) ? array() : $tasks;
	}

	/**
	 * @param string $columnName : le nom de la colonne de la table task
	 * @param string $order : ASC/DESC
	 * @return array : la liste des tâches de l'utilisateur de la session de manière selon l'ordre définit de la colonne
	 */
	public function getMyTasksOrdered(string $columnName = '', string $order = 'ASC') : array {
		Session::start();
		$userSession = unserialize($_SESSION[Session::getUserIndex()]);
		if (is_null($userSession) || !$userSession) {
			return array();
		}
		$tasks = $this->taskDAO->findAllByCreatorIdOrderByColumnName($userSession->getId(), $columnName, $order);
		return is_null($tasks) || empty($tasks) ? array() : $tasks;
	}

	/**
	 * @param int $kanban_id : l'identifiant du kanban
	 * @param string $columnName : le nom de la colonne de la table task
	 * @param string $order : ASC/DESC
	 * @return array : la liste des tâches d'un kanban selon l'ordre définit
	 */
	public function getTasksByKanbanIdOrdered(int $kanban_id = -1, string $columnName = '', string $order = 'ASC') : array {
		$tasks = $this->taskDAO->findAllByKanbanIdOrderByColumnName($kanban_id, $columnName, $order);
		return !is_null($tasks) && !empty($tasks) ? $tasks : array();
	}

	/**
	 * @param int $card_id : l'identifiant de la colonne
	 * @return array : la liste des tâches d'une colonne
	 */
	public function getTasksByCardId(int $card_id = -1) : array {
		$tasks = $this->taskDAO->findAllByCardId($card_id);
		return !is_null($tasks) && !empty($tasks) ? $tasks : array();
	}

	/**
	 * @param string $title : le titre de la tâche
	 * @param int $card_id : l'identifiant de la colonne
	 * @return Task|null : la tâche correspondant au titre à l'identifiant de la colonne 
	 */
	public function getTaskByTitleCardId(string $title = '', int $card_id = -1) : ?Task {
		$task = $this->taskDAO->findByTitleCardId($title, $card_id);
		return !is_null($task) ? $task : null;
	}

	/**
	 * @param Task $task : l'objet task à insérer
	 * @return Task|null : Effectue l'insertion et récupère l'objet Task inséré
	 */
	public function createTask(Task $task) : ?Task {
		if (is_null($task)) { return null; }
		// Insertion de la tâche en base de données
		// Insertion dans la table task
		if (!$this->taskDAO->insert($task)) {
			return null;
		}
		$taskId = $this->taskDAO->lastInsertId();
		if (is_null($taskId)) {
			// Normalement ici, il faut delete($task)
			return null;
		}
		// Récupère la tache insérée (car id a changé)
		$taskInserted = $this->taskDAO->findById($taskId);
		if (is_null($taskInserted)) {
			// $this->taskDAO->delete($taskInserted);
			return null;
		}
		return $taskInserted;
	}

	/**
	 * @param int $card_id : l'identifiant de la colonne
	 * @param int $id : l'identifiant de la tâche
	 * @return bool : Effectue la mis à jour et retourne true si la maj a été faite, false sinon
	 */
	public function updateCardIdById(int $card_id = -1, int $id = -1) : bool {
		if (!$this->taskDAO->updateCardIdById($card_id, $id)) {
			return false;
		}
		return true;
	}

}

?>