 <?php
 // This is a short work form.  Still looking how to intergrate it better into ParaDB.
 // The two vars the script needs is the case ID and owner ID to help create filenames
 $case = $_GET['case_id'];
 $owner= $_GET['owner_id'];
 //Set to 1 MB for now.  Got to be a better way, sysvar or something.
 $maxfilesize=1474560;

 //A little bit of security.  This will work for now.
 if ($case == "" || $owner == "") {
     die("Not a valid entry point");
 }
 else {
     if ($HTTP_POST_VARS['submit']) {


         if (!is_uploaded_file($HTTP_POST_FILES['file']['tmp_name'])) {
             $error = "You did not upload a file!";
             unlink($HTTP_POST_FILES['file']['tmp_name']);
         // assign error message, remove uploaded file, redisplay form.
         } else {
         //a file was uploaded
             if ($HTTP_POST_FILES['file']['size'] > $maxfilesize) {
                 $error = "File is larger than ".$maxfilesize." bytes.";
                 unlink($HTTP_POST_FILES['file']['tmp_name']);
             // assign error message, remove uploaded file, redisplay form.
             } else {
             //File has passed all validation, copy it to the final destination and remove the temporary file:
             // but first, let's strip out some common malicious files that might get uploaded, plus nasty chars:
                 $remove_these = array(' ','`','"','\'','\\','/','.htaccess','.exe','.vbs','.js');
                 $newname = str_replace($remove_these,'',$_HTTP_POST_FILES['file']['name']);
                 copy($HTTP_POST_FILES['file']['tmp_name'],"uploads/".$case."-".$owner."-".$newname);
                 unlink($HTTP_POST_FILES['file']['tmp_name']);
                 print "File".$case."-".$owner."-".$HTTP_POST_FILES['file']['name']." has been successfully uploaded!";
                 $error = "";
                 exit;
             }
         }
     }
 }

 ?>

<html>
    <head>
        <title>Upload a file</title>
    </head>
    <body>
        <form action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data">
            Choose a file to upload:<br>
            <input type="file" name="file"><br>
            <input type="submit" name="submit" value="submit"><br><br>
            <strong>Limit 1 MB!!</strong>
             <?=$error?>
        </form>
    </body>
</html>
