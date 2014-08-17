/**
 * Checks all the Checkboxes of an Form
 * 
 * @param field
 */
function checkAll(field) {
	for (i = 0; i < field.length; i++) {
		field[i].checked = true ;
	}
}

/**
 * Unchecks all the Checkboxes of an Form
 * 
 * @param field
 */
function uncheckAll(field) {
	for (i = 0; i < field.length; i++) {
		field[i].checked = false;
	}
}

function processUserUpdates() {
	user = users.pop();
	pars = {
		uid: user
	};
	var myAjax = new Zikula.Ajax.Request(
		Zikula.Config.baseURL + 'index.php?module=LDAPAuth&type=Ajax&func=updateADUser', 
		{
			parameters: pars,
			onComplete: processUserUpdates_response
		}
	);
}

function processUserUpdates_response(req) {
	data = req.getData();
	i++;
	$('progbar').style.width = (100 * (i) / len ).toFixed(1) + '%';
	$('percentage').innerHTML = (100 * (i) / len ).toFixed(1) + ' %';
	
	if (i < len) {
		processUserUpdates();
	} else {
		$('okbuttonarea').className = '';
	}
}

function processUserImports() {
	user = users.pop();
	$('currentuser').innerHTML = user;
	pars = {
		uname: user
	};
	var myAjax = new Zikula.Ajax.Request(
		Zikula.Config.baseURL + 'index.php?module=LDAPAuth&type=Ajax&func=importADUser',
		{
			parameters: pars,
			onComplete: processUserImports_response
		}
	);
}

function processUserImports_response(req) {
	data = req.getData();
	i++;
	$('progbar').style.width = (100 * (i) / len).toFixed(1) + '%';
	$('percentage').innerHTML = (100 * (i) / len).toFixed(1) + ' %';
	
	if (i < len) {
		processUserImports();
	} else {
		$('currentuser').innerHTML = '';
		$('okbuttonarea').className = '';
	}
}

function processGroupImports() {
	group = groups.pop();
	$('currentgroup').innerHTML = group;
	pars = {
		'group': group
	};
	var myAjax = new Zikula.Ajax.Request(
		Zikula.Config.baseURL + 'index.php?module=LDAPAuth&type=Ajax&func=importGroup',
		{
			parameters: pars,
			onComplete: processGroupImports_response
		}
	);
}

function processGroupImports_response(req) {
	data = req.getData();
	i++;
	$('progbar').style.width = (100 * (i) / len).toFixed(1) + '%';
	$('percentage').innerHTML = (100 * (i) / len).toFixed(1) + ' %';
	
	if (i < len) {
		processGroupImports();
	} else {
		$('currentgroup').innerHTML = '';
		$('okbuttonarea').className = '';
	}
}
