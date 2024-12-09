<?php
function connectToDB()
{
  $db_host = '127.0.0.1';
  $db_user = 'root';
  $db_password = 'root';
  $db_db = 'exoef';
  $db_port = 8889;

  try {
    $db = new PDO('mysql:host=' . $db_host . '; port=' . $db_port . '; dbname=' . $db_db, $db_user, $db_password);
  } catch (PDOException $e) {
    echo "Error!: " . $e->getMessage() . "<br />";
    die();
  }
  $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
  return $db;
}

function getUsers(): array
{
  $sql = "SELECT * FROM users ORDER BY id DESC";
  $stmt = connectToDB()->prepare($sql);
  $stmt->execute();

  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function existingUsername(String $username): bool
{
  $sql = "SELECT username FROM users WHERE username = :username";
  $stmt = connectToDB()->prepare($sql);
  $stmt->execute([':username' => $username]);
  return $stmt->fetch(PDO::FETCH_COLUMN);
}

function registerNewUser(String $firstname, String $lastname, String $username, String $mail, String $country, int $terms): bool|int|string
{
  $sql = "INSERT INTO users(firstname, lastname, username, email, country, terms, date_created) VALUES (:firstname, :lastname, :username, :email, :country, :terms, CURRENT_TIMESTAMP)";
  $stmt = connectToDB()->prepare($sql);
  $stmt->execute([
    ':firstname' => $firstname,
    ':lastname' => $lastname,
    ':username' => $username,
    ':email' => $mail,
    ':country' => $country,
    ':terms' => $terms
  ]);
  return connectToDB()->lastInsertId();
}
