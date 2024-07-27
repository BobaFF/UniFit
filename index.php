<?php
session_start();
$paginaHTML = file_get_contents("html/index.html");
if(isset($_SESSION['logged_in']))
    $paginaHTML = str_replace("<!--logout-->", "<li><a href=\"logout.php\">Logout</a></li>", $paginaHTML);
$footer = file_get_contents("html/footer.html");
$paginaHTML = str_replace("{footer}", $footer, $paginaHTML);
echo $paginaHTML;
?>