<?php
// Open the SQLite database (create it if not exists)
$db = new SQLite3("database.db");

// Check if the names table exists, if not, create it
$createTableQuery = "CREATE TABLE IF NOT EXISTS names (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT)";
$db->exec($createTableQuery);

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Retrieve and sanitize the entered name
  $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW);

  // Save the name to the database
  $insertQuery = "INSERT INTO names (name) VALUES (:name)";
  $stmt = $db->prepare($insertQuery);
  $stmt->bindValue(":name", $name, SQLITE3_TEXT);
  $stmt->execute();

  // Redirect to index.php
  header("Location: index.php");
  exit();
}

// Delete a name if the "delete" parameter is provided in the URL
if (isset($_GET["delete"])) {
  $nameToDelete = urldecode($_GET["delete"]);
  $deleteQuery = "DELETE FROM names WHERE name = :name";
  $stmt = $db->prepare($deleteQuery);
  $stmt->bindValue(":name", $nameToDelete, SQLITE3_TEXT);
  $stmt->execute();

  // Redirect to index.php
  header("Location: index.php");
  exit();
}

// Retrieve and display the saved names, sorted by descending order
$selectQuery = "SELECT * FROM names ORDER BY id DESC";
$result = $db->query($selectQuery);
?>


<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  </head>
  <body>
    <div class="container-fluid">
      <h2 class="mt-4 mb-4 text-center fw-bold">Save and Delete Names</h2>
      <form method="POST" action="">
        <div class="mb-2">
          <input type="text" class="form-control" id="nameInput" name="name" placeholder="enter name" maxlength="50" required>
        </div>
        <button type="submit" class="btn btn-primary w-100 fw-bold">Save</button>
      </form> 

      <h5 class="mb-3 mt-3 fw-bold">Saved Names:</h5>
      <?php
      if ($result) {
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $name = $row['name'];
        $id = $row['id'];
      ?>
        <div class="btn-group mb-2">
          <a class="btn btn-danger" onclick="return confirm('Are you sure?')" href="?delete=<?php echo urlencode($name); ?>">
            <i class="bi bi-trash-fill"></i>
          </a>
          <a href="#" class="btn btn-secondary fw-bold disabled">(<?php echo $id; ?>) <?php echo $name;?></a>
        </div></br>
      <?php }
      } ?>
    </div>
  </body>
</html>
