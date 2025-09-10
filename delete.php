<?php
if(!isset($_COOKIE['fname'])){
  header("Location:login.php");

}

?>
<?php
//1-get id from url
$id = $_GET['id'] ?? null;

if (!$id) {
    die(" No user ID provided!");
}

try {
  require "db.php";
  $user = new User();
  $user = $user->delete("users",$id);


    header("Location:list.php");

   
} catch (PDOException $e) {
    die(" DB error: " . $e->getMessage());
}
?>

