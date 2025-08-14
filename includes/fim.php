</main>
<script src="../assets/js/valida_formulario.js"></script>

<!--função do menu mobile-->
<script>
    function toggleMenu() {
        const menu = document.getElementById('menu');
        const overlay = document.getElementById('overlay-mobile');

        menu.classList.toggle("right-0");
        menu.classList.toggle("-right-full");

        overlay.classList.toggle('visible');
        overlay.classList.toggle('invisible');
    }
</script>

<!--div de erro-->
<?php if (isset($_SESSION['resposta'])): ?>
    <div id="div-erro"><i class="bi bi-info-circle-fill"></i> <?= htmlspecialchars($_SESSION['resposta']) ?></div>
    <?php
    unset($_SESSION['resposta']);
endif
?>
</body>

</html>