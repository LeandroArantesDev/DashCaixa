 </main>
 <?php if (isset($_SESSION['resposta'])): ?>
 <div id="div-erro"><i class="bi bi-info-circle-fill"></i> <?= htmlspecialchars($_SESSION['resposta']) ?></div>
 <?php
        unset($_SESSION['resposta']);
    endif
    ?>
 </body>

 </html>