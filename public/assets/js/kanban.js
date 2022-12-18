"use strict";
/*----------------------------------------------------------------------------*/
/*                                Définition AJAX                             */
/*----------------------------------------------------------------------------*/
/**
 * Source : https://stackoverflow.com/questions/8567114/how-can-i-make-an-ajax-call-without-jquery
*/
var ajax = {};
ajax.x = function () {
	if (typeof XMLHttpRequest !== 'undefined') {
		return new XMLHttpRequest();
	}
	var versions = [
		"MSXML2.XmlHttp.6.0",
		"MSXML2.XmlHttp.5.0",
		"MSXML2.XmlHttp.4.0",
		"MSXML2.XmlHttp.3.0",
		"MSXML2.XmlHttp.2.0",
		"Microsoft.XmlHttp"
	];
	var xhr;
	for (var i = 0; i < versions.length; i++) {
		try {
			xhr = new ActiveXObject(versions[i]);
			break;
		} catch (e) {
		}
	}
	return xhr;
};

ajax.send = function (url, callback, method, data, async) {
	if (typeof async === 'undefined') {
		async = true;
	}
	var x = ajax.x();
	x.open(method, url, async);
	x.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	x.onreadystatechange = function () {
		if (x.readyState == XMLHttpRequest.DONE && x.status == 200) {
			callback(x.responseText);
		} else {
			console.log(x.status + '-' + x.readyState + " => " + x.statusText);
			console.log(x.readyState == XMLHttpRequest.DONE)
		}
	};
	if (method == 'POST') {
		x.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	}
	x.send(data);
};

ajax.get = function (url, data, callback, async) {
	var query = [];
	for (var key in data) {
		query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
	}
	ajax.send(url + (query.length ? '?' + query.join('&') : ''), callback, 'GET', null, async)
};

ajax.post = function (url, data, callback, async) {
	var query = [];
	for (var key in data) {
		query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
	}
	ajax.send(url, callback, 'POST', query.join('&'), async);
};

/*----------------------------------------------------------------------------*/
/*                           Quelques prototypes utilitaires                  */
/*----------------------------------------------------------------------------*/
/**
 * Source : https://stackoverflow.com/questions/45202920/what-is-the-simplest-check-possible-for-an-html-js-injection-attack
*/
String.prototype.testInput = function() {
	return this
		.replace(/&/g, '&amp;')
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;")
		.replace(/"/g, '&quot;')
		.replace(/'/g, '&#39;');
}

Element.prototype.setAttributes = function(attributes) {
	for (var key in attributes) {
		this.setAttribute(key, attributes[key]);
	}
}

const scripts = document.getElementsByTagName('script');
const lastScript = scripts[scripts.length - 1];
const scriptName = lastScript.src;
const debug = true;
const log = function () {
	if (!debug) {
		return;
	}
	const d = new Date();
	const dateFormat = d.getDate() + '/' + (d.getMonth() + 1) + '/' + d.getFullYear() + ' ' + d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds(); 
	const logprefix = `${dateFormat} - [${scriptName}]`;
	// Conversion des arguments de la fonction en tableau
	const args = Array.prototype.slice.call(arguments);
	// Préfixe logprefix dans le tableau
	args.unshift(logprefix + ' : ');
	// Ajout des arguments à la console
	console.log.apply(console, args);
}

/*----------------------------------------------------------------------------*/
/*                                  KANBAN                                    */
/*----------------------------------------------------------------------------*/

const prefixUrl = 'http://localhost/Okanban/public/kanban/ajax/';
const param = window.location.href.substring(window.location.href.lastIndexOf('/') + 1);
const urls = {
	kanban: prefixUrl + param,
	newcardkanban: prefixUrl + 'ajouter-colonne/' + param,
	newtaskkanban: prefixUrl + 'ajouter-tache/' + param,
	updatetaskkanban: prefixUrl + 'maj-tache/' + param,
	inviteuserkanban: prefixUrl + 'inviter-utilisateur'
};

let kanban = null;
let cards = null;
let usernames = null;

const refreshKanban = function (delay) {
	let saveResponse = null;
	setInterval(function () {
		ajax.get(urls.kanban, {}, function (response) {
			if (saveResponse === response) {
				log('Réponse JSON identique');
				return;
			}
			if (saveResponse === null) {
				log('Première réponse JSON reçu');
				saveResponse = response;
			}
			let data = null;
			try {
				data = JSON.parse(response);	
			} catch (e) {
				log('Une erreur est survenue, lors de la récupération de la réponse du kanban');
				return;
			}
			// Construction du nouveau kanban suite à la nouvelle réponse
			kanban = data.kanban.kanban;
			cards = data.kanban.cards;
			usernames = data.kanban.usernames;
			buildKanban(kanban, cards, usernames);
			saveResponse = response;
		}, false);
		log('Nouvelle demande d\'une réponse du kanban');
	}, delay);
}

const delayRefresh = 1000;
refreshKanban(delayRefresh);


/*----------------------------------------------------------------------------*/
/*                            COMPOSANTS GRAPHIQUES                           */
/*----------------------------------------------------------------------------*/

const buildKanban = function (kanban, cards, usernames) {
	// Supprime l'ancien kaban s'il est présent
	const basemodel = document.getElementById("basemodel");
	while (basemodel.lastChild) {
		basemodel.removeChild(basemodel.lastChild);
	}
	// Supprime la liste des participants
	const participants = document.getElementById("participants");
	if (participants) {
		while (participants.lastChild) {
			participants.removeChild(participants.lastChild);
		}
		participants.remove();
	}

	const divKanban = document.createElement("div");
	 /* d-flex flex-nowrap overflow-auto */
	divKanban.setAttributes({"id": "kanban", "class" : "row flex-nowrap overflow-auto"} )
	// Colonnes
	cards.forEach(function(card) {
		const divKanbanCards = document.createElement("div");
		divKanbanCards.setAttribute("class", "col-lg-2 col-md-3 col-sm-5");
		divKanbanCards.append(buildCard(card, card.tasks));
		divKanban.appendChild(divKanbanCards);
	});
	// Bouton nouvelle colonne
	const divKanbanNewCard = document.createElement("div");
	divKanbanNewCard.setAttribute("class", "col-md-3");
	divKanbanNewCard.appendChild(buildButtonNewCard(kanban));
	divKanbanNewCard.appendChild(buildFormNewCard(kanban));
	divKanban.appendChild(divKanbanNewCard);
	basemodel.appendChild(divKanban);

	const divUserRow = document.createElement("div");
	divUserRow.setAttributes({"id": "participants", "class": "row p-3" });
	const divUserCol = document.createElement("div");
	divUserCol.setAttribute("class", "col");
	const titleParticipant = document.createElement("h2");
	titleParticipant.textContent = "Participant" + (usernames.length > 1 ? 's' : '');
	const ulUsers = document.createElement("ul");
	ulUsers.setAttribute("class", "list-inline");
	usernames.forEach(function(username) {
		const liUser = document.createElement("li");
		liUser.setAttribute("class", "list-inline-item m-1");
		const aUser = document.createElement("a");
		aUser.setAttributes({"href": "#", "class": "badge badge-primary"});
		aUser.textContent = username;
		liUser.appendChild(aUser);
		ulUsers.appendChild(liUser);
	});
	divUserCol.appendChild(titleParticipant);
	divUserCol.appendChild(ulUsers);
	divUserRow.appendChild(divUserCol);
	basemodel.after(divUserRow);
};

function buildCard(card, tasks) {
	if (!card || !tasks) { return;  }
	if (!card.title || typeof card.title === 'undefined') {
		card.title = "Pas de titre défini";
	}
	if (!tasks) {
		tasks = Array();
	}
	const divCard = document.createElement("div");
	divCard.setAttributes({"id" : "kanban-card-" + card.id, "class": "card mb-2 bg-light"});

	const divCardHeader = document.createElement("div");
	divCardHeader.classList.add("card-header");
	const pTitle = document.createElement("p");
	pTitle.setAttribute("class", "font-weight-bold text-capitalize");
	pTitle.textContent = card.title;

	const divCardBody = document.createElement("div");
	divCardBody.setAttribute("id", "kanban-card-body-" + card.id);
	divCardBody.classList.add("card-body");
	divCardBody.style.maxHeight = "50vh";
	divCardBody.style.overflowY = "auto";
	
	// Drop de la colonne
	dropCard(divCardBody, card);

	divCardHeader.appendChild(pTitle);
	tasks.forEach(function(task) {
		divCardBody.appendChild(buildTask(card, task));
	});

	const divCardFooter = document.createElement("div");
	divCardFooter.setAttribute("class", "card-footer");
	
	divCard.appendChild(divCardHeader);
	divCard.appendChild(divCardBody);
	divCardFooter.appendChild(buildButtonNewTask(card));
	divCardFooter.appendChild(buildFormNewTask(card));
	divCard.appendChild(divCardFooter);

	return divCard;
}

function buildTask(card, task) {
	const divTask = document.createElement("div");
	divTask.setAttributes({
		"id" : "kanban-task-" + task.task.id,
		"class" : "card m-2 bg-white",
		"draggable": "true"
	});
	dragTask(divTask, card, task);
	
	const divTaskHeader = document.createElement("div");
	divTaskHeader.classList.add("card-header");
	const pTitle = document.createElement("p");
	pTitle.setAttribute("class", "text-capitalize");
	pTitle.textContent = task.task.title;
	const sLimitDate = document.createElement("small");
	sLimitDate.setAttribute("class", "text-muted font-weight-bold");
	let strDate = task.task.limit_date;
	let date = new Date(strDate);
	sLimitDate.textContent = !strDate ? "" : ("Avant le " + date.toLocaleDateString("fr"));

	const divTaskBody = document.createElement("div");
	divTaskBody.classList.add("card-body");
	const pDescription = document.createElement("p");
	pDescription.textContent = task.task.description;

	const divTaskFooter = document.createElement("div");
	divTaskFooter.classList.add("card-footer");

	const ulUsers = document.createElement("ul");
	ulUsers.setAttribute("class", "list-inline");
	task.usernames.forEach(function(username) {
		const liUser = document.createElement("li");
		liUser.setAttribute("class", "list-inline-item m-1");
		const aUser = document.createElement("a");
		aUser.setAttributes({"href": "#", "class": "badge badge-primary"});
		aUser.textContent = username;
		liUser.appendChild(aUser);
		ulUsers.appendChild(liUser);
	});

	divTaskHeader.appendChild(pTitle);
	divTaskHeader.appendChild(sLimitDate);
	divTaskBody.appendChild(pDescription);
	divTaskFooter.appendChild(ulUsers);
	divTask.appendChild(divTaskHeader);
	divTask.appendChild(divTaskBody);
	divTask.appendChild(divTaskFooter);

	return divTask;
}

function buildButtonNewCard(kanban) {
	let buttonNewCard = document.createElement("button");
	buttonNewCard.setAttributes({
		"type": "button",
		"class": "btn btn-primary m-2",
		"data-toggle": "collapse",
		"href": `#kanban-newcard-${kanban.id}-collapse`,
		"role": "button",
		"aria-expanded": "false",
		"aria-controls": `kanban-newcard-${kanban.id}-collapse`
	});
	buttonNewCard.textContent = "Ajouter une colonne";
	return buttonNewCard;
}

function buildFormNewCard(kanban) {
	const divCollapse = document.createElement("div");
	divCollapse.setAttributes({"id": `kanban-newcard-${kanban.id}-collapse`, "class": "collapse"});
	const divCard = document.createElement("div");
	divCard.setAttributes({"id": "kanban-newcard", "class": "card card-body" });
	const form = document.createElement("form");
	form.setAttributes({"method": "POST", "action": ""});
	
	const divFormTitle = document.createElement("div");
	divFormTitle.setAttribute("class", "form-group");
	const labelTitle = document.createElement("label");
	labelTitle.setAttribute("for", "kanban-newcard-title");
	labelTitle.setAttribute("class", "col-form-label");
	labelTitle.textContent = "Titre";
	const inputTitle = document.createElement("input");
	inputTitle.setAttributes({
		"id": "kanban-newcard-title", "type": "text",
		"name": "kanban-newcard-title", "class": "form-control"
	});
	
	const divFormSubmit = document.createElement("div");
	divFormSubmit.setAttribute("class", "form-group");
	const inputSubmit = document.createElement("input");
	inputSubmit.setAttributes({
		"type": "submit",
		"name": "submit",
		"class": "btn btn-primary",
		"value": "Créer"
	});

	divFormTitle.appendChild(labelTitle);
	divFormTitle.appendChild(inputTitle);
	divFormSubmit.appendChild(inputSubmit);
	form.appendChild(divFormTitle);
	form.appendChild(divFormSubmit);
	divCard.appendChild(form);
	divCollapse.appendChild(divCard);

	form.onsubmit = function(event) {
		event.preventDefault();
		const data = {
			title: document.getElementById('kanban-newcard-title').value.testInput()
		}
		ajax.post(urls.newcardkanban, {data: JSON.stringify(data)}, function(response) {
			let data = {};
			let classState = "";
			console.log(response)
			try {
				data = JSON.parse(response);
				if (data.connected === true && data.valid !== undefined && data.valid === true) {
					classState = "alert-success";
				} else {
					classState = "alert-danger";
				}
			} catch(e) {
				console.log(e);
			}
			// Message d'information
			const divAlert = document.createElement("div");
			divAlert.setAttributes({
				"id": "invite-alert",
				"class": `alert ${classState} position-relative`
			});
			divAlert.textContent = data.message;
			const kanbanNewCard = document.getElementById("kanban-newcard");
			const oldDivAlert = document.getElementById(`invite-alert-kanban-newcard-${kanban.id}`);
			if (oldDivAlert) {
				const parent = kanbanNewCard.parentNode;
				parent.removeChild(oldDivAlert);
			}
			kanbanNewCard.after(divAlert);
			// Ajout de la colonne
			const divKanban = document.getElementById("kanban");
			const divCard = document.createElement("div");
			divCard.setAttribute("class", "col-lg-2 col-md-3 col-sm-5");
			const newCard = buildCard(data.card, null);
			if (newCard) {
				divCard.append(buildCard(data.card, null));
				divKanban.insertBefore(divCard, divKanban.lastChild);
			}
			form.reset();
		}, false);
	}
	return divCollapse;
}

function buildButtonNewTask(card) {
	const buttonNewTask = document.createElement("button");
	buttonNewTask.setAttributes({
		"type": "button",
		"class": "btn btn-primary m-2",
		"data-toggle": "collapse",
		"href": `#newtask-card-${card.id}`,
		"role": "button",
		"aria-expanded": "false",
		"aria-controls": `newtask-card-${card.id}`
	});
	buttonNewTask.textContent = "Ajouter une tâche à " + (card.title.charAt(0).toUpperCase() + card.title.slice(1));
	return buttonNewTask;
}

function buildFormNewTask(card) {
	const divCollapse = document.createElement("div");
	divCollapse.setAttributes({
		"id": `newtask-card-${card.id}`,
		"class": "collapse"
	});
	const divCard = document.createElement("div");
	divCard.setAttributes({
		"id": `newtask-${card.title}`,
		"class": "card card-body"
	});

	const form = document.createElement("form");
	form.setAttributes({
		"method": "POST",
		"action": ""
	});
	
	const divFormTitle = document.createElement("div");
	divFormTitle.setAttribute("class", "form-group");
	const labelTitle = document.createElement("label");
	labelTitle.setAttributes({"for": `newtask-${card.id}-title`, "class": "col-form-label"});
	labelTitle.textContent = "Titre";
	const inputTitle = document.createElement("input");
	inputTitle.setAttributes({
		"id": `newtask-${card.id}-title`,
		"type": "text",
		"name": "title",
		"class": "form-control"
	});

	const divFormDescription = document.createElement("div");
	divFormDescription.setAttribute("class", "form-group");
	const labelDescription = document.createElement("label");
	labelDescription.setAttributes({"for": `newtask-${card.id}-description`, "class": "col-form-label"});
	labelDescription.textContent = "Description";
	const textareaDescription = document.createElement("textarea");
	textareaDescription.setAttributes({
		"id": `newtask-${card.id}-description`,
		"name": "description",
		"class": "form-control"
	});

	const divFormLimitDate = document.createElement("div");
	divFormLimitDate.setAttribute("class", "form-group");
	const labelLimitDate = document.createElement("label");
	labelLimitDate.setAttributes({"for": `newtask-${card.id}-limitdate`, "class": "col-form-label"});
	labelLimitDate.textContent = "Date limite (optionnel)";
	const inputLimitDate = document.createElement("input");
	inputLimitDate.setAttributes({
		"id": `newtask-${card.id}-limitdate`,
		"type": "datetime-local",
		"name": "limitdate",
		"class": "form-control"
	});
	
	const divUsers = new Array();
	usernames.forEach(function(username) {
		const attributeId = `newtask-${card.title}-user-${username}`;
		const divFormUser = document.createElement("div");
		divFormUser.setAttribute("class", "form-check form-check-inline custom-control custom-checkbox");
		const labelUser = document.createElement("label");
		labelUser.setAttributes({"for": attributeId, "class": "custom-control-label"});
		labelUser.textContent = username;
		const inputUser = document.createElement("input");
		inputUser.setAttributes({
			"type": "checkbox",
			"id": attributeId,
			"name": attributeId,
			"class": "custom-control-input"
		});
		divFormUser.appendChild(inputUser);
		divFormUser.appendChild(labelUser);
		divUsers.push(divFormUser);
	});

	const divFormSubmit = document.createElement("div");
	divFormSubmit.setAttribute("class", "form-group");
	const inputSubmit = document.createElement("input");
	inputSubmit.setAttributes({
		"type": "submit",
		"name": "submit",
		"class": "btn btn-primary",
		"value": "Envoyer"
	});

	divFormTitle.appendChild(labelTitle);
	divFormTitle.appendChild(inputTitle);
	divFormDescription.appendChild(labelDescription);
	divFormDescription.appendChild(textareaDescription);
	divFormLimitDate.appendChild(labelLimitDate);
	divFormLimitDate.appendChild(inputLimitDate);
	divFormSubmit.appendChild(inputSubmit);

	form.appendChild(divFormTitle);
	form.appendChild(divFormDescription);
	form.appendChild(divFormLimitDate);
	divUsers.forEach(function(divFormUser) {
		form.appendChild(divFormUser);
	});
	form.appendChild(inputSubmit);

	divCard.appendChild(form);
	divCollapse.appendChild(divCard);

	form.onsubmit = function(event) {
		event.preventDefault();
		let inputs = divUsers.map(element => element.getElementsByTagName("input").item(0));
		let inputsChecked = inputs.filter(input => input.checked === true);
		let arrUsernames = [];
		inputsChecked.forEach(input => {
			arrUsernames.push(input.parentNode.getElementsByTagName('label').item(0).textContent);
		})
		let data = {
			card: card,
			title: inputTitle.value.testInput(),
			description: textareaDescription.value.testInput(),
			limitdate: inputLimitDate.value.testInput(),
			usernames: arrUsernames
		}
		ajax.post(urls.newtaskkanban, { data: JSON.stringify(data) }, function (response) {
			log(response);
			let data = {};
			let classState = "";
			try {
				data = JSON.parse(response);
				if (data.connected === true && data.valid !== undefined && data.valid === true) {
					classState = "alert-success";
				} else {
					classState = "alert-danger";
				}
			} catch(e) {
				console.log(e);
			}
			// Message d'information
			const divAlert = document.createElement("div");
			divAlert.setAttribute("id", `invite-alert-newtask--${card.id}`);
			divAlert.setAttribute("class", `alert ${classState} position-relative`);
			divAlert.textContent = data.message;
			const taskTitle = document.getElementById("newtask-" + card.title);
			const oldDivAlert = document.getElementById(`invite-alert-newtask--${card.id}`);
			if (oldDivAlert) {
				const parent = taskTitle.parentNode;
				parent.removeChild(oldDivAlert);
			}
			const divCard = document.getElementById("newtask-" + card.title);
			divCard.after(divAlert);
			// Ajout de la tâche
			if (data.connected && data.valid) {
				const divCardBody = document.getElementById("kanban-card-body-" + card.id);
				divCardBody.appendChild(buildTask(card, data.task));
			}
			form.reset();
		}, false);
	}
	return divCollapse;
}

// Bouton d'invitation pour inviter à un utilisateur à un kanban
const btnInvitation = document.getElementById('btnInvitation');
btnInvitation.onclick = function(event) {
	event.preventDefault();
	const inputInvitation = document.getElementById("iptInvitation");
	ajax.post(urls.inviteuserkanban, {
		kanban: JSON.stringify(kanban), username: inputInvitation.value.testInput()}, function(response) {
		let data = {valid: false, message: ''};
		let classState = "";
		try {
			data = JSON.parse(response);
			if (data.connected === true && data.valid !== undefined && data.valid === true) {
				classState = "alert-success";
			} else {
				classState = "alert-danger";
			}
		} catch(e) {
			log('Erreur d\'analyse de la réponse sur le bouton invitation');
		}
		const divAlert = document.createElement("div");
		divAlert.setAttribute("id", "invite-alert-btninvitation");
		divAlert.setAttribute("class", `alert ${classState} position-relative`);
		divAlert.textContent = data.message;
		if (document.getElementById('invite-alert-btninvitation') !== null) {
			document.getElementById('divInvitation').parentNode.removeChild(document.getElementById('invite-alert-btninvitation'));
		}
		document.getElementById('divInvitation').after(divAlert);		
	}, false);
};


/*----------------------------------------------------------------------------*/
/*                                 DRAG AND DROP                              */
/*----------------------------------------------------------------------------*/

/**
 * @param {HTMLDivElement} element : la div a appliqué le drag
 * @param {Object} card : la colonne source
 * @param {Object} task  : la tâche a drag
 */
 const dragTask = function (element, card, task) {
	element.addEventListener('dragstart', function (event) {
		const data = {
			"id": this.id,
			"taskTitle": task.task.title,
			"cardTitleSource": card.title
		};
		event.dataTransfer.setData("application/json", JSON.stringify(data));
		event.dataTransfer.effectAllowed = "move";
    });
};

/**
 * @param {HTMLDivElement} element : la div a appliqué le drop
 * @param {Object} card : la colonne de destination
 */
 const dropCard = function (element, card) {
	element.addEventListener('drop', function (event) {
		event.preventDefault();
		let dataTransfer = null;
		try {
			dataTransfer = JSON.parse(event.dataTransfer.getData("application/json"));
		} catch (error) {
			console.log(error);
			return;
		}
		const droppedElement = document.getElementById(dataTransfer.id);
		const droppedElementParent = droppedElement.parentNode;
		const data = {
			taskTitle: dataTransfer.taskTitle.testInput(),
			cardTitleSource: dataTransfer.cardTitleSource.testInput(),
			cardTitleDestination: card.title.testInput()

		};
		ajax.post(urls.updatetaskkanban, { data: JSON.stringify(data) }, function (response) {
			let data = {};
			try {
				data = JSON.parse(response);
				if (data.connected === true && data.valid !== undefined && data.valid === true) {
					droppedElementParent.removeChild(droppedElement);
					element.appendChild(droppedElement);
				}
			} catch (e) {
				console.log(e);
			}
		}, false);

	}, false);
	element.addEventListener('dragover', function (event) {
		event.preventDefault();
		event.dataTransfer.dropEffect = "move";
	}, false);
};