<?php
error_reporting(E_ALL ^ E_WARNING);
$domain = "https://任.cc.cd";
$sitesDir = __DIR__."/sites";
if(!is_dir($sitesDir)) mkdir($sitesDir,0755,true);

if($_SERVER["REQUEST_METHOD"] === "POST"){
    //安全校验
    $file = $_FILES["zipfile"];
    $ext = strtolower(pathinfo($file["name"],PATHINFO_EXTENSION));
    if($ext !== "zip") exit("只能上传zip压缩包");
    if($file["size"]>50*1024*1024) exit("最大50MB");
    $mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE),$file["tmp_name"]);
    if($mime !== "application/zip") exit("不是合法zip文件");

    //生成唯一目录ID
    $id = uniqid().mt_rand(1000,9999);
    $target = $sitesDir."/".$id;
    mkdir($target,0755,true);

    //解压
    $zip = new ZipArchive();
    if($zip->open($file["tmp_name"])===true){
        $zip->extractTo($target);
        $zip->close();
        $link = $domain."/sites/".$id."/";
        echo "<div style='padding:20px'>部署成功<br><a href='$link' target='_blank'>$link</a></div>";
    }else{
        echo "解压失败，压缩包损坏";
    }
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf‑8">
<title>站点部署</title>
</head>
<body style="padding:30px">
<h3>上传zip部署网站</h3>
<form method="post" enctype="multipart/form-data">
<input type="file" name="zipfile" accept=".zip" required>
<button type="submit">部署</button>
</form>
</body>
</html>
