<?php
// Database setup script
$host = 'localhost';
$username = 'root';
$password = '';

echo "<h2>Setting up Class Project Database...</h2>\n";

try {
    // Connect to MySQL server (without specifying database)
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Connected to MySQL server successfully<br>\n";

    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS class_project");
    echo "✅ Database 'class_project' created successfully<br>\n";

    // Select the database
    $pdo->exec("USE class_project");
    echo "✅ Selected 'class_project' database<br>\n";

    // Read and execute the schema file
    $schema = file_get_contents('database/schema.sql');

    if ($schema === false) {
        throw new Exception("Could not read schema.sql file");
    }

    // Split the schema into individual statements
    $statements = array_filter(array_map('trim', explode(';', $schema)));

    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^--/', $statement)) {
            try {
                $pdo->exec($statement);
            } catch (PDOException $e) {
                // Ignore table exists errors and comments
                if (strpos($e->getMessage(), 'already exists') === false &&
                    strpos($statement, 'CREATE DATABASE') === false) {
                    echo "⚠️ Warning executing: " . substr($statement, 0, 50) . "...<br>\n";
                    echo "Error: " . $e->getMessage() . "<br>\n";
                }
            }
        }
    }

    echo "✅ Database schema created successfully<br>\n";

    // Verify the setup by checking tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "✅ Tables created: " . implode(', ', $tables) . "<br>\n";

    // Check sample data
    $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $courseCount = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();

    echo "✅ Sample data loaded: $userCount users, $courseCount courses<br>\n";

    echo "<br><strong>🎉 Database setup completed successfully!</strong><br>\n";
    echo "<a href='index.php' style='background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 10px; display: inline-block;'>Go to Application</a>\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>\n";
    echo "<br>Please check your database configuration in config/database.php<br>\n";
}
?>
