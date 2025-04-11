<?php
// Definir la variable $busqueda con el valor del parámetro 'search' en la URL, o una cadena vacía si no está presente
$busqueda = isset($_GET['search']) ? $_GET['search'] : '';
?>
<div class="container is-fluid mb-6">   
    <h1 class="title">Productos</h1>
    <h2 class="subtitle">Lista de productos</h2>
</div>

<div class="container pb-6 pt-6">
    <!-- Barra de búsqueda y Checkbox -->
    <div class="field is-grouped mb-5" style="display: flex; align-items: center;">
        <!-- Barra de búsqueda (ocupa la mitad de la pantalla) -->
        <div class="control" style="flex: 1; max-width: 70%; margin-right: 10px;">
            <input type="text" id="searchInput" class="input" placeholder="Buscar productos..." value="<?php echo $busqueda; ?>" style="width: 100%;" />
        </div>

        <!-- Checkbox (al costado derecho de la barra de búsqueda) -->
        <div class="control">
            <label class="checkbox">
                <input type="checkbox" id="inStockCheckbox" <?php echo isset($_GET['in_stock']) && $_GET['in_stock'] == '1' ? 'checked' : ''; ?> />
                <strong>Solo con Stock</strong>
            </label>
        </div>
    </div>

    <!-- Contenedor para los productos -->
    <div id="productList">
        <?php
            require_once "./php/main.php";

            # Eliminar producto #
            if (isset($_GET['product_id_del'])) {
                require_once "./php/producto_eliminar.php";
            }

            if (!isset($_GET['page'])) {
                $pagina = 1;
            } else {
                $pagina = (int) $_GET['page'];
                if ($pagina <= 1) {
                    $pagina = 1;
                }
            }

            $categoria_id = (isset($_GET['category_id'])) ? $_GET['category_id'] : 0;

            $pagina = limpiar_cadena($pagina);
            $url = "index.php?vista=product_list&page=";
            $registros = 15;
            $busqueda = isset($_GET['search']) ? $_GET['search'] : "";
            $in_stock = isset($_GET['in_stock']) ? $_GET['in_stock'] : 0;

            # Paginador producto #
            require_once "./php/producto_lista.php";
        ?>
    </div>
</div>
<script src="path/to/dropdowns.js"></script>
<script>
    // Función para activar los dropdowns
function activarDropdowns() {
    const dropdowns = document.querySelectorAll(".dropdown");
    dropdowns.forEach((dropdown) => {
        const trigger = dropdown.querySelector(".dropdown-trigger");
        trigger.addEventListener("click", () => {
            dropdown.classList.toggle("is-active");
        });
    });

    // Cerrar el dropdown al hacer clic fuera de él
    document.addEventListener("click", (event) => {
        dropdowns.forEach((dropdown) => {
            if (!dropdown.contains(event.target)) {
                dropdown.classList.remove("is-active");
            }
        });
    });
}

// Función para actualizar la lista de productos
function updateProductList() {
    var searchQuery = document.getElementById('searchInput').value;
    var inStock = document.getElementById('inStockCheckbox').checked ? 1 : 0;
    var page = <?php echo $pagina; ?>;

    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'product_list_ajax.php?page=' + page + '&search=' + encodeURIComponent(searchQuery) + '&in_stock=' + inStock, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById('productList').innerHTML = xhr.responseText;
            activarDropdowns(); // Activar dropdowns después de actualizar el contenido
        }
    };
    xhr.send();
}

// Detectar cambios en el campo de búsqueda
document.getElementById('searchInput').addEventListener('input', function() {
    updateProductList(); // Actualizar la lista de productos al escribir en el campo de búsqueda
});

// Detectar cambios en el checkbox
document.getElementById('inStockCheckbox').addEventListener('change', function() {
    updateProductList(); // Actualizar la lista de productos al marcar/desmarcar el checkbox
});

</script>
