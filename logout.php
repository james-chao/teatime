<?php
header( 'Content-Type: text/html; charset=utf-8' );

session_unset();
session_destroy();
setcookie("teatimeuuser","",time()-3600);
setcookie("teatimeutype","",time()-3600);
setcookie("teatimeuserno","",time()-3600);
setcookie("teatimeuflag","",time()-3600);

echo "<script language=javascript>alert('您已成功登出！');location.href='/';</script>";
?> 