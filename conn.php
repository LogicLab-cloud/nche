// Database connection parameters
$dbHost = 'localhost';
$dbName = 'u953672825_BornToCode';
$dbUser = 'u953672825_born2code';
$dbPass = '!Prince1347';

// Create a new PDO instance
try {
$db = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
echo "Connection failed: " . $e->getMessage();
}