<?php
if (move_uploaded_file($_FILES['tes']['tmp_name'], 'uploads/' . $_FILES['tes']['name'])) {
    echo "✅ Upload berhasil!";
} else {
    echo "❌ Upload GAGAL. Periksa izin folder.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="" method="POST" enctype="multipart/form-data">
      <input type="file" name="tes" />
      <button type="submit">Upload</button>
    </form>
    
</body>
</html>