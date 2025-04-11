<?php
// Validación y sanitización de variables
$pagina = (int) ($_GET['pagina'] ?? 1);
$search = limpiar_cadena($_GET['search'] ?? '');
$in_stock = (int) ($_GET['in_stock'] ?? 0);
$orden = isset($_GET['orden']) ? limpiar_cadena($_GET['orden']) : 'producto_nombre';

// Cálculo de paginación
$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;
$tabla = "";

// Campos a seleccionar
$campos = "producto.producto_id, producto.producto_codigo, producto.producto_nombre, producto.producto_precio, producto.producto_stock, producto.producto_foto, producto.categoria_id, producto.usuario_id, categoria.categoria_id, categoria.categoria_nombre, usuario.usuario_id, usuario.usuario_nombre, usuario.usuario_apellido";

// Condiciones de búsqueda y filtros
$condiciones = [];

if (!empty($search)) {
    $condiciones[] = "(producto.producto_codigo LIKE '%$search%' OR producto.producto_nombre LIKE '%$search%')";
}

if ($in_stock == 1) {
    $condiciones[] = "producto.producto_stock > 0";
}

// Consulta de datos
$consulta_datos = "SELECT $campos 
                  FROM producto 
                  INNER JOIN categoria ON producto.categoria_id = categoria.categoria_id 
                  INNER JOIN usuario ON producto.usuario_id = usuario.usuario_id";

if (count($condiciones) > 0) {
    $consulta_datos .= " WHERE " . implode(' AND ', $condiciones);
}

$consulta_datos .= " ORDER BY $orden ASC LIMIT $inicio, $registros";

// Consulta de total
$consulta_total = "SELECT COUNT(producto_id) FROM producto";

if (count($condiciones) > 0) {
    $consulta_total .= " WHERE " . implode(' AND ', $condiciones);
}

// Ejecución de consultas
$conexion = conexion();
$datos = $conexion->query($consulta_datos);
$datos = $datos->fetchAll();

$total = $conexion->query($consulta_total);
$total = (int) $total->fetchColumn();

$Npaginas = ceil($total / $registros);

// Generación de la tabla
if ($total >= 1 && $pagina <= $Npaginas) {
    $contador = $inicio + 1;
    $pag_inicio = $inicio + 1;
    foreach ($datos as $rows) {
        
        $tabla .= '
        
        <article class="media" style="padding-top: 0; padding-bottom: 5px; margin-top: 0; margin-bottom: 5px; display: flex; align-items: center; justify-content: space-between;">
        <div style="display: flex; align-items: center;">
            <figure class="media-left">
                <p class="image" style="width: 90px; height: 90px;">';
                if (is_file("./img/producto/" . $rows['producto_foto'])) {
                    $tabla .= '<img src="./img/producto/' . $rows['producto_foto'] . '">';
                } else {
                    $tabla .= '<img src="./img/producto.png">';
                }
            $tabla .= '</p>
            </figure>
            <div class="media-content">
                <div class="content">
                  <p>
                    <strong>' . $rows['producto_nombre'] . '</strong><br>
                    <strong>CODIGO:</strong> ' . $rows['producto_codigo'] . ', <strong>PRECIO:</strong> $' . $rows['producto_precio'] . ', <strong>STOCK:</strong> ' . $rows['producto_stock'] . ', <strong>CATEGORIA:</strong> ' . $rows['categoria_nombre'] . '
                  </p>
                </div>
            </div>
        </div>
        <div class="dropdown is-right">
            <div class="dropdown-trigger">
                <button class="button is-success is-rounded is-small boton-mas" aria-haspopup="true" aria-controls="dropdown-menu-' . $rows['producto_id'] . '">
    <span><strong>···</strong></span>
</button>
            </div>
            <div class="dropdown-menu" id="dropdown-menu-' . $rows['producto_id'] . '" role="menu">
                <div class="dropdown-content">
                    <a href="index.php?vista=product_img&product_id_up=' . $rows['producto_id'] . '" class="dropdown-item">Imagen</a>
                    <a href="index.php?vista=product_update&product_id_up=' . $rows['producto_id'] . '" class="dropdown-item">Actualizar</a>
                    <a href="' . $url . $pagina . '&product_id_del=' . $rows['producto_id'] . '" class="dropdown-item">Eliminar</a>
                </div>
            </div>
        </div>
    </article>';
        $contador++;
    }
    $pag_final = $contador - 1;
} 
else {
    if ($total >= 1) {
        $tabla .= '
            <p class="has-text-centered" >
                <a href="' . $url . '1" class="button is-link is-rounded is-small mt-4 mb-4">
                    Haga clic acá para recargar el listado
                </a>
            </p>
        ';
    } else {
        $tabla .= '
            <p class="has-text-centered" >No hay registros en el sistema</p>
        ';
    }
}

if ($total > 0 && $pagina <= $Npaginas) {
    $tabla .= '<p class="has-text-right">Mostrando productos <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>';
}

$conexion = null;
echo $tabla;

if ($total >= 1 && $pagina <= $Npaginas) {
    echo paginador_tablas($pagina, $Npaginas, $url, 7);
}
?>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        // Activar todos los dropdowns
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
    });
</script>