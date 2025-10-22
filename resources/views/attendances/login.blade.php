<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiosco de Asistencias</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .kiosk-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 3rem;
            max-width: 500px;
            width: 90%;
            text-align: center;
        }
        .logo {
            font-size: 4rem;
            color: #667eea;
            margin-bottom: 1rem;
        }
        .form-control {
            height: 60px;
            font-size: 1.2rem;
            border-radius: 15px;
            border: 2px solid #e9ecef;
            text-align: center;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            height: 60px;
            font-size: 1.3rem;
            font-weight: bold;
            border-radius: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            margin-top: 1rem;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .time-display {
            font-size: 1.5rem;
            color: #6c757d;
            margin-bottom: 2rem;
        }
        .status-message {
            margin-top: 1rem;
            padding: 1rem;
            border-radius: 10px;
            font-weight: bold;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .keypad {
            display: none;
            margin-top: 2rem;
        }
        .keypad-btn {
            width: 80px;
            height: 80px;
            font-size: 2rem;
            margin: 5px;
            border-radius: 50%;
            border: 2px solid #667eea;
            background: white;
            color: #667eea;
        }
        .keypad-btn:hover {
            background: #667eea;
            color: white;
        }
        .instructions {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 1rem;
        }
        .employee-info {
            background: #e3f2fd;
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
            display: none;
        }
    </style>
</head>
<body>
    <div class="kiosk-container">
        <div class="logo">
            <i class="fas fa-clock"></i>
        </div>
        
        <h2 class="mb-4">Control de Asistencias</h2>
        
        <div class="time-display" id="currentTime"></div>

        <form id="loginForm">
            <div class="form-group">
                <input type="text" 
                       class="form-control" 
                       id="dni" 
                       name="dni" 
                       placeholder="Ingrese su DNI"
                       maxlength="8"
                       pattern="[0-9]{8}"
                       required>
            </div>
            
            <div class="form-group">
                <input type="password" 
                       class="form-control" 
                       id="password" 
                       name="password" 
                       placeholder="Contraseña"
                       required>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-login">
                <i class="fas fa-sign-in-alt"></i> MARCAR ASISTENCIA
            </button>
        </form>

        <!-- Keypad Virtual (opcional para pantallas táctiles) -->
        <div class="keypad" id="keypad">
            <div class="row justify-content-center">
                <div class="col-auto">
                    <button type="button" class="btn keypad-btn" onclick="addDigit('1')">1</button>
                    <button type="button" class="btn keypad-btn" onclick="addDigit('2')">2</button>
                    <button type="button" class="btn keypad-btn" onclick="addDigit('3')">3</button>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-auto">
                    <button type="button" class="btn keypad-btn" onclick="addDigit('4')">4</button>
                    <button type="button" class="btn keypad-btn" onclick="addDigit('5')">5</button>
                    <button type="button" class="btn keypad-btn" onclick="addDigit('6')">6</button>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-auto">
                    <button type="button" class="btn keypad-btn" onclick="addDigit('7')">7</button>
                    <button type="button" class="btn keypad-btn" onclick="addDigit('8')">8</button>
                    <button type="button" class="btn keypad-btn" onclick="addDigit('9')">9</button>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-auto">
                    <button type="button" class="btn keypad-btn" onclick="clearInput()">
                        <i class="fas fa-backspace"></i>
                    </button>
                    <button type="button" class="btn keypad-btn" onclick="addDigit('0')">0</button>
                    <button type="button" class="btn keypad-btn" onclick="submitForm()">
                        <i class="fas fa-check"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="employee-info" id="employeeInfo">
            <h5 id="employeeName"></h5>
            <p id="employeeDetails"></p>
        </div>

        <div id="statusMessage"></div>

        <div class="instructions">
            <small>
                <i class="fas fa-info-circle"></i>
                Ingrese su DNI y contraseña para registrar su entrada o salida.
                <br>
                Si no recuerda su contraseña, contacte al administrador.
            </small>
        </div>

        <!-- Botón para mostrar teclado virtual en dispositivos táctiles -->
        <button type="button" class="btn btn-link mt-3" onclick="toggleKeypad()">
            <i class="fas fa-keyboard"></i> Teclado Virtual
        </button>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            updateTime();
            setInterval(updateTime, 1000);

            // Auto-focus en DNI
            $('#dni').focus();

            // Solo números en DNI
            $('#dni').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            // Auto-submit cuando DNI tiene 8 dígitos y hay contraseña
            $('#dni, #password').on('input', function() {
                if ($('#dni').val().length === 8 && $('#password').val().length > 0) {
                    setTimeout(() => $('#loginForm').submit(), 500);
                }
            });

            // Form submission
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                processLogin();
            });

            // Limpiar mensajes después de 5 segundos
            $(document).on('DOMNodeInserted', '#statusMessage', function() {
                setTimeout(() => $('#statusMessage').fadeOut(), 5000);
            });
        });

        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleString('es-PE', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            $('#currentTime').text(timeString);
        }

        function processLogin() {
            const dni = $('#dni').val();
            const password = $('#password').val();

            if (dni.length !== 8) {
                showMessage('El DNI debe tener 8 dígitos', 'error');
                return;
            }

            if (!password) {
                showMessage('Ingrese su contraseña', 'error');
                return;
            }

            // Deshabilitar el formulario
            $('input, button').prop('disabled', true);
            $('.btn-login').html('<i class="fas fa-spinner fa-spin"></i> PROCESANDO...');

            $.post('{{ route("attendance-kiosk.login") }}', {
                dni: dni,
                password: password,
                _token: '{{ csrf_token() }}'
            })
            .done(function(response) {
                if (response.success) {
                    showMessage(response.message, 'success');
                    showEmployeeInfo(response.employee, response.type, response.datetime);
                    clearForm();
                }
            })
            .fail(function(xhr) {
                const error = xhr.responseJSON;
                showMessage(error.message || 'Error al procesar la solicitud', 'error');
            })
            .always(function() {
                // Rehabilitar el formulario después de 3 segundos
                setTimeout(() => {
                    $('input, button').prop('disabled', false);
                    $('.btn-login').html('<i class="fas fa-sign-in-alt"></i> MARCAR ASISTENCIA');
                    $('#dni').focus();
                }, 3000);
            });
        }

        function showMessage(message, type) {
            const alertClass = type === 'success' ? 'success' : 'error';
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
            
            $('#statusMessage').html(`
                <div class="status-message ${alertClass}">
                    <i class="fas ${icon}"></i> ${message}
                </div>
            `).show();
        }

        function showEmployeeInfo(employee, type, datetime) {
            const typeText = type === 'entry' ? 'ENTRADA' : 'SALIDA';
            const typeIcon = type === 'entry' ? 'fa-sign-in-alt' : 'fa-sign-out-alt';
            const typeColor = type === 'entry' ? 'text-success' : 'text-warning';
            
            $('#employeeName').html(`<i class="fas fa-user"></i> ${employee}`);
            $('#employeeDetails').html(`
                <span class="${typeColor}">
                    <i class="fas ${typeIcon}"></i> <strong>${typeText}</strong>
                </span><br>
                <small class="text-muted">${datetime}</small>
            `);
            $('#employeeInfo').slideDown();
            
            // Ocultar después de 5 segundos
            setTimeout(() => $('#employeeInfo').slideUp(), 5000);
        }

        function clearForm() {
            setTimeout(() => {
                $('#dni').val('');
                $('#password').val('');
                $('#statusMessage').fadeOut();
            }, 3000);
        }

        // Funciones del teclado virtual
        function toggleKeypad() {
            $('#keypad').toggle();
        }

        function addDigit(digit) {
            const dniInput = $('#dni');
            if (dniInput.val().length < 8) {
                dniInput.val(dniInput.val() + digit);
                dniInput.focus();
            }
        }

        function clearInput() {
            const dniInput = $('#dni');
            dniInput.val(dniInput.val().slice(0, -1));
            dniInput.focus();
        }

        function submitForm() {
            if ($('#dni').val().length === 8 && $('#password').val()) {
                $('#loginForm').submit();
            } else {
                $('#password').focus();
            }
        }

        // Eventos de teclado
        $(document).keydown(function(e) {
            // ESC para limpiar
            if (e.keyCode === 27) {
                clearForm();
                $('#statusMessage').fadeOut();
                $('#employeeInfo').slideUp();
            }
            
            // F1 para mostrar/ocultar teclado
            if (e.keyCode === 112) {
                e.preventDefault();
                toggleKeypad();
            }
        });

        // Auto-reload cada 5 minutos para mantener la sesión fresca
        setInterval(() => {
            if ($('#dni').val() === '' && $('#password').val() === '') {
                location.reload();
            }
        }, 300000);
    </script>
</body>
</html>