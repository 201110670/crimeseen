var CrimeSeen =
{
	/* properties */
	$signup_form: null,
	$login_form: null,
	/* initialization functions */
	initLoginRegistrationSwitch: function($)
	{
		$("#hideReg").click(function() {
			$(this).hide();
			$.mobile.loading('show');
			$("#hideme").slideToggle();
			
			$("#toggle").slideToggle(function() {
				$.mobile.loading('hide');
			});
			
			$("#hideReg2").show();
		});
		
		 $("#hideReg2").click(function() {
			$(this).hide();
			$.mobile.loading('show');
			$("#hideme").slideToggle();
			
			$("#toggle").slideToggle(function() {
				$.mobile.loading('hide');	
			});
			
			$("#hideReg").show();
		 });
	 
		$("#hideReg1").click(function(){
			$.mobile.loading('show');
			
			location.href = "index.html";
			
			$.mobile.loading('hide');
		});
	}, // [\initLoginRegistrationSwitch]
	
	initSignUpForm: function($)
	{
		CrimeSeen.$signup_form = $("#signupform");
		
		CrimeSeen.$signup_form.validate({
			errorPlacement: function ($error, $element) {
				var name = $element.attr("name");
				$("#error" + name).html($error);
			},
			rules: {
				fullname: {
					required: true,
					minlength: 1
				},
				
				email: {
					required: true,
					email: true
				},
				
				username: {
					required: true,
					minlength: 8
				},
				pass: {
					required: true,
					minlength: 8
				},
				cpass: {
					required: true,
					minlength: 8,
					equalTo: "#pass"
				}
			
			},
			messages: {
				fullname: {
					required: "Please enter your Full Name"
				},
				
				email: "Please enter a valid email address",
			
				username: {
					required: "Please enter a valid username",
					minlength: "Your username must be at least 8 characters long"
				},
				pass: {
					required: "Please provide a password",
					minlength: "Your password must be at least 8 characters long"
				},
				cpass: {
					required: "Please provide a password",
					minlength: "Your password must be at least 8 characters long",
					equalTo: "Passwords does not match"
				}
			}
		});
		
		CrimeSeen.$signup_form.submit(function(e) {
			e.preventDefault();
			
			if ( ! CrimeSeen.$signup_form.valid()) {
				return false;
			}
			
			// validate if email and/or username exists in the database
			$.getJSON(
				"./phpfiles/userval.php?callback=?",
				CrimeSeen.$signup_form.serialize(),
				function(data) {
					// uexists = username exists
					if(data.uexists =="exists") {
						$("body").scrollTop($("#username").offset().top);
						$("#errorusename").text("");
						$("#errorusername").append("Username <b>"+ data.username + "</b> is already in use, use another username<br>");
					} else {
						$.getJSON(
							"./phpfiles/uregister.php?callback=?",
							CrimeSeen.$signup_form.serialize(),
							function(data) {
								location.href = "index.html";
							}
						).fail(function(data){
							alert("failed");
						});
					}
				}
			).fail(function(data){
				alert("validation failed");
			});
		});
	}, // [\initSignUpForm]
	
	initLoginForm: function($)
	{
		CrimeSeen.$login_form = $("#hideme");
		
		CrimeSeen.$login_form.submit(function(e) {
			e.preventDefault();
		
			if ($( "#lusername" ).val() == "" || $( "#lpass" ).val() == "") {
				return false;
			}
			
			$.getJSON(
				"./phpfiles/verifyfirst.php?callback=?",
				CrimeSeen.$login_form.serialize(),
				function(data) {
					if (data.verified == "v1") {
						localStorage.setItem("datauser", data.username);
						location.href="post.html";
					} else {
						alert("problem with your Account please try again.");
					}
				}
			).fail(function(data){
				alert("failed to log in");
			});
		});
	}, // [\initLoginForm]
	
	redirectLoggedIn: function()
	{
		if (localStorage.getItem("datauser") !== null && localStorage.getItem("datauser") != "") {
			location.href = "post.html";
		}
	} // [\redirectLoggedIn]
};
/* Actual Initialization */
(function($) {
	CrimeSeen.redirectLoggedIn();
	CrimeSeen.initLoginRegistrationSwitch($);
	CrimeSeen.initSignUpForm($);
	CrimeSeen.initLoginForm($);
})(jQuery);