<?php
$connFile = '/var/www/vendor/tursodatabase/turso-driver-laravel/src/Database/LibSQLConnection.php';
if (file_exists($connFile)) {
    $connContent = file_get_contents($connFile);
    
    if (strpos($connContent, 'protected function run(') === false) {
        $runMethod = '

    protected function run($query, $bindings, \Closure $callback)
    {
        $hasNamed = false;
        foreach ($bindings as $key => $val) {
            if (is_string($key)) { $hasNamed = true; break; }
        }
        
        if (!$hasNamed && !empty($bindings)) {
            $i = 0;
            $vals = array_values($bindings);
            $count = count($vals);
            
            $query = preg_replace_callback("/\?/", function() use (&$i, $count) {
                if ($i < $count) {
                    return ":p" . $i++;
                }
                return "?";
            }, $query);
            
            $named = [];
            foreach ($vals as $idx => $val) {
                $named[":p$idx"] = $val;
            }
            $bindings = $named;
        }

        return parent::run($query, $bindings, $callback);
    }
}';
        $connContent = preg_replace('/\}\s*$/', $runMethod, $connContent);
        file_put_contents($connFile, $connContent);
        echo "Patched LibSQLConnection ->run()\n";
    }
}

$stmtFile = '/var/www/vendor/tursodatabase/turso-driver-laravel/src/Database/LibSQLPDOStatement.php';
if (file_exists($stmtFile)) {
    $content = file_get_contents($stmtFile);
    $old = 'if ($this->hasNamedParameters($parameters)) {
            $this->statement->bindNamed($parameters);
        } else {
            $this->statement->bindPositional(array_values($parameters));
        }';
    $new = '$namedParams = [];
        if ($this->hasNamedParameters($parameters)) {
            $namedParams = $parameters;
        } else {
            foreach (array_values($parameters) as $idx => $val) {
                $namedParams[":p$idx"] = $val;
            }
        }
        $this->statement->bindNamed($namedParams);';
    $patched = str_replace($old, $new, $content);
    if ($patched !== $content) {
        file_put_contents($stmtFile, $patched);
        echo "Patched LibSQLPDOStatement bindPositional\n";
    } else {
        echo "LibSQLPDOStatement - pattern not found, skipping\n";
    }
}

$connFile2 = '/var/www/vendor/tursodatabase/turso-driver-laravel/src/Database/LibSQLConnection.php';
$content2 = file_get_contents($connFile2);

$old2 = '            $statement = $this->getRawPdo()->prepare($query);

            $results = $statement->query($bindings);';

$new2 = '            if (!empty($bindings) && !(count(array_filter(array_keys($bindings), "is_string")) > 0)) {
                $i = 0;
                $count = count($bindings);
                $query = preg_replace_callback("/\?/", function() use (&$i, $count) {
                    return $i < $count ? ":p" . $i++ : "?";
                }, $query);
                $named = [];
                foreach (array_values($bindings) as $idx => $val) {
                    $named[":p$idx"] = $val;
                }
                $bindings = $named;
            }
            $statement = $this->getRawPdo()->prepare($query);
            if (!empty($bindings)) {
                $statement->bindNamed($bindings);
                $results = $statement->query([]);
            } else {
                $results = $statement->query($bindings);
            }';

$patched2 = str_replace($old2, $new2, $content2);
if ($patched2 !== $content2) {
    file_put_contents($connFile2, $patched2);
    echo "Patched LibSQLConnection select() direct query\n";
} else {
    echo "LibSQLConnection select() direct - pattern not found\n";
}
