function toggleSubmenu() {
  const submenu = document.getElementById("mensalidade-submenu");
  const seta = document.getElementById("mensalidade-seta");
  submenu.classList.toggle("hidden");
  // Alterna o ícone entre "chevron-right" e "chevron-down"
  if (submenu.classList.contains("hidden")) {
    seta.classList.remove("bi-chevron-down");
    seta.classList.add("bi-chevron-right");
  } else {
    seta.classList.remove("bi-chevron-right");
    seta.classList.add("bi-chevron-down");
  }
}
