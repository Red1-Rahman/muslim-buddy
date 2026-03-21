<?php
$dbFile = '/var/www/vendor/tursodatabase/turso-driver-laravel/src/Database/LibSQLDatabase.php';
if (file_exists($dbFile)) {
    $dbContent = file_get_contents($dbFile);
    $oldDb = 'public function prepare(string $sql): LibSQLPDOStatement
    {
        return new LibSQLPDOStatement(';
    $newDb = 'public function prepare(string $sql): LibSQLPDOStatement
    {
        $i = 0;
        $sql = preg_replace_callback("/\?/", function() use (&$i) {
            return ":p" . $i++;
        }, $sql);
        return new LibSQLPDOStatement(';
    
    $dbContent = str_replace($oldDb, $newDb, $dbContent);
    file_put_contents($dbFile, $dbContent);
    echo "Patched LibSQLDatabase ->prepare()\n";
}

$stmtFile = '/var/www/vendor/tursodatabase/turso-driver-laravel/src/Database/LibSQLPDOStatement.php';
if (file_exists($stmtFile)) {
    $stmtContent = file_get_contents($stmtFile);
    
    $pattern = '/if \(\$this->hasNamedParameters\(\$parameters\)\) \{\s+\$this->statement->bindNamed\(\$parameters\);\s+\} else \{\s+\$this->statement->bindPositional\(array_values\(\$parameters\)\);\s+\}/';
    
    $replacement = 'if (!$this->hasNamedParameters($parameters)) {
            $named = [];
            foreach (array_values($parameters) as $idx => $val) {
                $named[":p$idx"] = $val;
            }
            $parameters = $named;
        }
        $this->statement->bindNamed($parameters);';
        
    $stmtContent = preg_replace($pattern, $replacement, $stmtContent);
    file_put_contents($stmtFile, $stmtContent);
    echo "Patched LibSQLPDOStatement positional params\n";
}
