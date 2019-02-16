<h2>Upload Image</h2>
<form method="post" enctype="multipart/form-data" action="editor.php">
  <input name="file" type="file"> <br>
  <input 
    type="radio"
    name="visibility"
    value="1"
    checked="checked"
  > Public
  <input 
    type="radio"
    name="visibility"
    value="0"
  > Private<br>
  <button type="submit" name="upload">Upload</button>
</form>