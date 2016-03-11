<?php

/*
  if ($_SERVER['SERVER_ADDR'] != '127.0.0.1') {

  echo "<table style='border: solid 1px black;'>";
  echo "<tr><th>Id</th><th>Firstname</th><th>Lastname</th></tr>";

  class TableRows extends RecursiveIteratorIterator {

  function __construct($it) {
  parent::__construct($it, self::LEAVES_ONLY);
  }

  function current() {
  return "<td style='width:150px;border:1px solid black;'>" . parent::current() . "</td>";
  }

  function beginChildren() {
  echo "<tr>";
  }

  function endChildren() {
  echo "</tr>" . "\n";
  }

  }

  $servername = "mysqlcluster7";
  #$servername = "127.0.0.1";
  $username = "kellstan_frank";
  $password = "123InYouGo!";

  echo '<h1>TEST DB - PDO CONNECT</h1>';

  try {
  echo 'PDO: ';
  $conn = new PDO("mysql:host=$servername;port=3306;dbname=edith", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $stmt = $conn->prepare("SELECT * FROM productcollections");
  $stmt->execute();
  // set the resulting array to associative
  $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
  foreach (new TableRows(new RecursiveArrayIterator($stmt->fetchAll())) as $k => $v) {
  echo $v;
  }
  } catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
  }
  }
  $conn = null;
  echo "</table>";
  exit();
 */

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
#echo 'PATH: ' . dirname(__DIR__);
chdir(dirname(__DIR__));

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if (__FILE__ !== $path && is_file($path)) {
        return false;
    }
    unset($path);
}


// Setup autoloading
require 'init_autoloader.php';

// Set Locale
date_default_timezone_set('Europe/London');

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
