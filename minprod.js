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
	var id_login_input = document.getElementById('id_login_input');
	console.log(id_login_input);
	id_login_input.placeholder = placeholder;
}