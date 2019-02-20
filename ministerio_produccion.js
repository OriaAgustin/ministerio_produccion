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
	var id_login_input = document.getElementById('login_id_input');
	console.log(id_login_input);
	id_login_input.placeholder = placeholder;
}

function login(){
	var select = document.getElementById('login_type_select');
	var login_type = select.options[select.selectedIndex].value;
	var login_id = document.getElementById('login_id_input').value;
	var login_password = document.getElementById('login_password_input').value;
	
	var login = {login_type: login_type, login_id: login_id, login_password: login_password};
	
	minAjax({
	    url:"index.php",
	    type:"POST",
	    data:{
	      ajax: "login",
	      /*login_type: login_type,
	      login_id: login_id,
	      login_password: login_password*/
	      login: JSON.stringify(login)
	    },
	    success: function(response){
	    	console.log(response);
	    	var body = document.getElementsByTagName('body');
	    	body = body[0];
	    	body.innerHTML = response;
	    }
  	});
}

function edit_item(item){
	minAjax({
	    url:"index.php",//request URL
	    type:"POST",//Request type GET/POST
	    //Send Data in form of GET/POST
	    data:{
	      ajax:"edit_item",
	      item: JSON.stringify(item)
	    },
	    //CALLBACK FUNCTION with RESPONSE as argument
	    success: function(response){
	    	console.log("LLEGUE");
	    	console.log(response);
	    	var body = document.getElementsByTagName('body');
	    	body = body[0];
	    	body.innerHTML = response;
	    }
  	});
}

function save_edition(){

}

function cancel_edition(){
	minAjax({
	    url:"index.php",//request URL
	    type:"POST",//Request type GET/POST
	    //Send Data in form of GET/POST
	    data:{
	      ajax:"edit_item",
	      item: JSON.stringify(item)
	    },
	    //CALLBACK FUNCTION with RESPONSE as argument
	    success: function(response){
	    	console.log("LLEGUE");
	    	console.log(response);
	    	var body = document.getElementsByTagName('body');
	    	body = body[0];
	    	body.innerHTML = response;
	    }
  	});
}

function delete_item(item){
	console.log("HOLA HOLA");
	minAjax({
    url:"index.php",//request URL
    type:"GET",//Request type GET/POST
    //Send Data in form of GET/POST
    data:{
      ajax:"prueba"
    },
    //CALLBACK FUNCTION with RESPONSE as argument
    success: function(data){
      var body = document.getElementsByTagName('body');
      body = body[0];
      console.log(body[0]);
      console.log(data);
      body.innerHTML = body.innerHTML + data;
    }
  });
}