// dropdowns.js
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

// Activar dropdowns cuando la página se carga
document.addEventListener("DOMContentLoaded", activarDropdowns);