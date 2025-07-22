<?php
function formatarPreco($numero)
{
    return 'R$ ' . number_format($numero, 2, ',', '.');
}
