<?php
if(isset($_FILES['file'])){
    $file_name = $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];
    move_uploaded_file($file_tmp, "uploads/".$file_name);
    echo "File uploaded: uploads/".$file_name;
}
?>
<form action="" method="POST" enctype="multipart/form-data">
    <input type="file" name="file" />
    <input type="submit" value="Upload" />
</form>
