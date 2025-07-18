 </main>
 <script>
     <?php
        if (isset($_SESSION['resposta'])) {
            echo "alert('" . $_SESSION['resposta'] . "');";
            unset($_SESSION['resposta']);
        }
        ?>
 </script>
 </body>

 </html>