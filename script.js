function showForm(formId) {
    // Hide all forms
    var forms = document.querySelectorAll('.form-section');
    forms.forEach(function(form) {
        form.classList.remove('active');
    });

    // Show the selected form
    document.getElementById(formId).classList.add('active');
}

// Initialize the page to show the Login Usuarios form by default
showForm('loginUsuarios');

// Handle the form submissions
document.getElementById('loginFormUsuarios').addEventListener('submit', function(event) {
    event.preventDefault();

    var formData = new FormData(this);
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'http://localhost:666/usuarios/login', true);
    
    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            document.getElementById('responseText').textContent = JSON.stringify(JSON.parse(xhr.responseText), null, 2);
        } else {
            document.getElementById('responseText').textContent = 'Error: ' + xhr.status;
        }
    };

    xhr.onerror = function() {
        document.getElementById('responseText').textContent = 'Request failed';
    };

    xhr.send(formData);
});

document.getElementById('loginFormSocios').addEventListener('submit', function(event) {
    event.preventDefault();

    var formData = new FormData(this);
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'http://localhost:666/usuarios/login', true);
    
    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            document.getElementById('responseText').textContent = JSON.stringify(JSON.parse(xhr.responseText), null, 2);
        } else {
            document.getElementById('responseText').textContent = 'Error: ' + xhr.status;
        }
    };

    xhr.onerror = function() {
        document.getElementById('responseText').textContent = 'Request failed';
    };

    xhr.send(formData);
});

document.getElementById('loginFormAgregarUsuarios').addEventListener('submit', function(event) {
    event.preventDefault();

    var formData = new FormData(this);
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'http://localhost:666/usuarios/alta', true);

    var token = document.getElementById('token').value;
    xhr.setRequestHeader('Authorization', 'Bearer ' + token);

    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            document.getElementById('responseText').textContent = JSON.stringify(JSON.parse(xhr.responseText), null, 2);
        } else {
            document.getElementById('responseText').textContent = 'Error: ' + xhr.status;
        }
    };

    xhr.onerror = function() {
        document.getElementById('responseText').textContent = 'Request failed';
    };

    xhr.send(formData);
});

document.getElementById('comandaForm').addEventListener('submit', function(event) {
    event.preventDefault();

    var formData = new FormData(this);
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'http://localhost:666/comanda', true);
    
    // Agregar el token Bearer en el encabezado
    xhr.setRequestHeader('Authorization', 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MjIwOTUyODcsImV4cCI6MTcyMjE1NTI4NywiZGF0YSI6eyJ1c3VhcmlvIjoic29jaW9tYXRpYXMiLCJ0aXBvX3VzdWFyaW8iOiJzb2NpbyJ9LCJhcHAiOiJMYSBDb21hbmRhIC0gSkMifQ.vttPZObMPF-goB44wrCOmfTtZoVnHi7qO4QJcX1zhZ0');

    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            document.getElementById('responseText').textContent = JSON.stringify(JSON.parse(xhr.responseText), null, 2);
        } else {
            document.getElementById('responseText').textContent = 'Error: ' + xhr.status;
        }
    };

    xhr.onerror = function() {
        document.getElementById('responseText').textContent = 'Request failed';
    };

    xhr.send(formData);
});