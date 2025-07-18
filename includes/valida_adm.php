<?php
if ($_SESSION['tipo'] != 1) {
    header("Location: " . BASE_URL . "pages/vendas");
}
