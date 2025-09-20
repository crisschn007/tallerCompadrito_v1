<?php
if (
    isset($_SESSION['titulo']) && isset($_SESSION['mensaje'])
    && isset($_SESSION['icono'])
) {
    $titulo = $_SESSION['titulo'];
    $mensaje = $_SESSION['mensaje'];
    $icono = $_SESSION['icono'];
?>
    <script>
        Swal.fire({
            title: '<?php echo $titulo; ?>', // Título dinámico
            text: '<?php echo $mensaje; ?>', // Mensaje adicional
            icon: '<?php echo $icono; ?>' // success, error, warning, info, question

        });
    </script>
<?php
    unset($_SESSION['titulo']);
    unset($_SESSION['mensaje']);
    unset($_SESSION['icono']);
}
?>