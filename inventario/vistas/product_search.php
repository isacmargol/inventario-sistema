<div class="container is-fluid mb-6">
    <h1 class="title">Productos</h1>
    <h2 class="subtitle">Buscar producto</h2>
</div>

<div class="container pb-6 pt-6">
    
    <?php
        require_once "./php/main.php";

        // Si el formulario de búsqueda ha sido enviado
        if (isset($_POST['modulo_buscador'])) {
            require_once "./php/buscador.php"; // Este archivo procesa la búsqueda
        }

        // Formulario de búsqueda
    ?>
<form action="" method="POST" autocomplete="off">
    <input type="hidden" name="modulo_buscador" value="producto">
    <div class="field is-grouped">
        <p class="control is-expanded">
            <input class="input is-rounded" type="text" name="txt_buscador" placeholder="¿Qué estás buscando?" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}" maxlength="30">
        </p>
        <p class="control">
            <button class="button is-info" type="submit">Buscar</button>
        </p>
    </div>

    <!-- Casilla de verificación para filtrar por productos con stock -->
    <div class="field">
        <input id="stock_check" type="checkbox" name="solo_stock" value="1" 
        <?php echo (isset($_POST['solo_stock']) && $_POST['solo_stock'] == '1') ? 'checked' : ''; ?>>
        <label for="stock_check">Mostrar solo productos con stock</label>
    </div>
</form>

    <?php
        // Mostrar mensaje si se está buscando algo
        if (isset($_SESSION['busqueda_producto']) && !empty($_SESSION['busqueda_producto'])) {
            echo '<div class="columns"><div class="column">';
            echo '<form class="has-text-centered mt-6 mb-6" action="" method="POST" autocomplete="off">';
            echo '<input type="hidden" name="modulo_buscador" value="producto"> ';
            echo '<input type="hidden" name="eliminar_buscador" value="producto">';
            echo '<p>Estás buscando <strong>“' . $_SESSION['busqueda_producto'] . '”</strong></p>';
            echo '<br>';
            echo '<button type="submit" class="button is-danger is-rounded">Atrás</button>';
            echo '</form></div></div>';
        }

        // Paginación de productos
        $pagina = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $pagina = ($pagina <= 1) ? 1 : $pagina;

        // Filtrado de productos si hay búsqueda
        $categoria_id = isset($_GET['category_id']) ? $_GET['category_id'] : 0;
        $pagina = limpiar_cadena($pagina);  // Función para limpiar la variable de la página
        $url = "index.php?vista=product_search&page=";  // URL de paginación
        $registros = 15;  // Cantidad de productos por página

        // Obtener los productos, si hay búsqueda, se pasa el término de búsqueda
        $busqueda = isset($_SESSION['busqueda_producto']) ? $_SESSION['busqueda_producto'] : '';

        // Verificar si la casilla de "solo stock" está marcada
        $solo_stock = isset($_POST['solo_stock']) && $_POST['solo_stock'] == '1' ? true : false;

        // Condición de stock
        $condicion_stock = '';
        if ($solo_stock) {
            $condicion_stock = "AND producto.producto_stock > 0";  // Asegúrate de que "producto_stock" es el nombre del campo en la base de datos
        }

        // Incluir el archivo que obtiene la lista de productos, pasándole la condición de stock
        require_once "./php/producto_lista.php";  // Este archivo obtiene la lista de productos, modificado para tener en cuenta el stock

        // Mostrar los productos
        if (isset($productos) && !empty($productos)) {
            echo '<div class="columns is-multiline">';
            foreach ($productos as $producto) {
                echo '<div class="column is-4">';
                echo '<div class="card">';
                echo '<div class="card-content">';
                echo '<p class="title">' . $producto['nombre'] . '</p>';
                echo '<p class="subtitle">$' . number_format($producto['precio'], 2) . '</p>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<p>No se encontraron productos para tu búsqueda.</p>';
        }
    ?>
</div>