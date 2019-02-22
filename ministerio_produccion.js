function change_profile_type(event){
	var select = event.target;
	var selection = select.options[select.selectedIndex];
	selection = selection.value;
	switch(selection){
		case "empresa":
			var placeholder = "Cuit";
			break
		case "usuario_interno":
			var placeholder = "Email";
			break;
	}
	var login_id_input = document.getElementById('login_id_input');
	login_id_input.value = "";
	login_id_input.placeholder = placeholder;
	var login_password_input = document.getElementById('login_password_input');
	login_password_input.value = "";
}

function login(){
	var select = document.getElementById('login_type_select');
	var login_type = select.options[select.selectedIndex].value;
	var login_id = document.getElementById('login_id_input').value;
	var login_password = document.getElementById('login_password_input').value;
	
	var login = {login_type: login_type, login_id: login_id, login_password: login_password};
	var ajax_data = {ajax: "login", login: JSON.stringify(login)}

	ajax_updater(ajax_data);
}

function edit_item(item_id){
	var pk_td = document.getElementById('pk_item_' + item_id);
	
	var pk_value = pk_td.innerText;

	var ajax_data = {ajax: "edit_item", pk_value: pk_value}

	ajax_updater(ajax_data);
}

function save_edited_item(){
	var new_values = document.getElementsByClassName('new_values');
	
	var data = { };
	for(let new_value of new_values){
	    if(new_value.value != ""){
			data[new_value.attributes.field.value] = new_value.value;
		}
	}

	var pk_td = document.getElementById('pk_value');
	data['pk_value'] = pk_td.innerText;

	var ajax_data = {ajax: "save_edited_item", data: JSON.stringify(data)};

	ajax_updater(ajax_data);
}

function delete_item(item_id){
	var pk_td = document.getElementById('pk_item_' + item_id);
	
	var pk_value = pk_td.innerText;

	var ajax_data = {ajax: "delete_item", pk_value: pk_value}

	ajax_updater(ajax_data);
}

function add_item(){
	var ajax_data = {ajax: "add_item"}

	ajax_updater(ajax_data);
}

function save_added_item(){
	var added_values = document.getElementsByClassName('added_values');

	var data = { };
	var index = 0;
	for(let added_value of added_values){
	    data[added_value.attributes.field.value] = added_value.value;
		index++;
	}

	var ajax_data = {ajax: "save_added_item", data: JSON.stringify(data)};

	ajax_updater(ajax_data);
}

function cancel(){
	go_to_main_view();
}

function go_to_main_view(){
	var ajax_data = {ajax: "main_view"};

  	ajax_updater(ajax_data);
}

function generate_report(){
	var ajax_data = {ajax: "generate_report"}

	ajax_updater(ajax_data);
}

function logout(){
	var ajax_data = {ajax: "logout"};

  	ajax_updater(ajax_data);
}

function ajax_updater(data){
	minAjax({
	    url:"index.php",
	    type:"POST",
	    data: data,
	    success: function(response){
	    	var body = document.getElementsByTagName('body');
	    	body = body[0];
	    	body.innerHTML = response;
	    }
  	});
}