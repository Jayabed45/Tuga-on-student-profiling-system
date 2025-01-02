// Toggle between login and create account forms
document.getElementById('createAccountLink').addEventListener('click', function() {
document.getElementById('loginForm').style.display = 'none';
document.getElementById('createAccountForm').style.display = 'block';
});

document.getElementById('loginLink').addEventListener('click', function() {
 document.getElementById('loginForm').style.display = 'block';
 document.getElementById('createAccountForm').style.display = 'none';
});