<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Iniciar Sesion</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="https://images.vexels.com/media/users/3/314312/isolated/preview/38a8261f2fe6c7afa195c61ec324f638-un-silenciador-de-coche.png" alt="Icono de silenciador">
</head>


<style>
    body {
        background-image: url('https://autopos.es/wp-content/uploads/2024/04/nissens.webp');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        font-family: 'Source Sans 3', sans-serif;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.5s ease, color 0.5s ease;
    }

    body.dark-mode {
        background-color: #121212 !important;
        color: #eaeaea !important;
    }

    .login-card {
        max-width: 400px;
        border-radius: 15px;
        animation: fadeIn 0.8s ease-in-out;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .login-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    .dark-mode .card {
        background-color: #1e1e1e;
        color: #eaeaea;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .input-group:focus-within {
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25);
        border-radius: 8px;
    }

    .btn-primary {
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: scale(1.03);
    }

    .btn-outline-dark,
    .btn-outline-secondary {
        transition: all 0.3s ease;
    }

    .btn-outline-dark:hover,
    .btn-outline-secondary:hover {
        transform: scale(1.05);
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-15px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Botón flotante para modo claro/oscuro */
    #themeToggle {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1050;
    }

    /* Subtítulo normal */
    .subtitle {
        color: #6c757d;
        /* igual que .text-muted en Bootstrap */
        transition: color 0.4s ease;
    }

    /* Subtítulo en modo oscuro */
    body.dark-mode .subtitle {
        color: #e0e0e0;
        /* gris claro para contraste */
    }
</style>

<body class="d-flex align-items-center justify-content-center vh-100">

<?php
        session_start();
        if (isset($_SESSION['mensaje'])) {
            $respuesta = htmlspecialchars($_SESSION['mensaje'], ENT_QUOTES, 'UTF-8'); ?>
            <script defer>
                document.addEventListener("DOMContentLoaded", () => {
                    Swal.fire({
                        title: "<?php echo $titulo; ?>", //Mencionar algo en el mensaje
                        text: "<?php echo $mensaje; ?>",
                        icon: "<?php echo $icono; ?>", //Cambia a "success", "warning", etc. según necesites
                        confirmButtonText: "Aceptar"
                    });
                });
            </script>
        <?php
            unset($_SESSION['titulo'], $_SESSION['mensaje']);
        }
        ?>


    <!-- Botón modo claro/oscuro -->
    <button type="button" class="btn btn-sm btn-outline-dark" id="themeToggle">
        <i class="bi bi-moon-fill"></i>
    </button>

    <div class="card shadow-lg p-4 login-card">
        <div class="text-center mb-3">
            <!-- Logo opcional -->
            <i class="bi bi-tools fs-1 text-danger mb-2"></i>
            <h3 class="fw-bold">Taller El Compadrito</h3>
            <p class="subtitle">Inicia Sesión</p>
        </div>


        <form action="../app/controllers/auth/ingreso.php" method="POST">
            <!-- Usuario -->
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                    <input type="text" name="username" class="form-control" required>
                </div>
            </div>

            <!-- Contraseña -->
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" id="password" name="password" class="form-control" required>
                    <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                        <i class="bi bi-eye-fill"></i>
                    </button>
                </div>
            </div>

            <!-- Botón login -->
            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-outline-danger">Ingresar</button>
            </div>
        </form>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mostrar / ocultar contraseña
        document.getElementById("togglePassword").addEventListener("click", function() {
            const passwordInput = document.getElementById("password");
            const icon = this.querySelector("i");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                icon.classList.replace("bi-eye-fill", "bi-eye-slash-fill");
            } else {
                passwordInput.type = "password";
                icon.classList.replace("bi-eye-slash-fill", "bi-eye-fill");
            }
        });

        // Modo oscuro/claro con localStorage
        const body = document.body;
        const themeToggle = document.getElementById("themeToggle");
        const currentTheme = localStorage.getItem("theme");

        if (currentTheme === "dark") {
            body.classList.add("dark-mode");
            themeToggle.innerHTML = '<i class="bi bi-brightness-high-fill"></i>';
        }

        themeToggle.addEventListener("click", () => {
            body.classList.toggle("dark-mode");
            if (body.classList.contains("dark-mode")) {
                localStorage.setItem("theme", "dark");
                themeToggle.innerHTML = '<i class="bi bi-brightness-high-fill"></i>';
            } else {
                localStorage.setItem("theme", "light");
                themeToggle.innerHTML = '<i class="bi bi-moon-fill"></i>';
            }
        });
    </script>

    
</body>

</html>