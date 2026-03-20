<?php
$file = '/var/www/vendor/tursodatabase/turso-driver-laravel/src/Database/LibSQLPDOStatement.php';
$content = file_get_contents($file);

$old = 'return $this->statement->execute($params);';

$new = '$i = 0;
if (!empty($params) && is_int(array_key_first($params))) {
    $this->query = preg_replace_callback("/\?/", function() use (&$i) {
        return ":p" . $i++;
    }, $this->query);
    $named = [];
    foreach (array_values($params) as $idx => $val) {
        $named[":p$idx"] = $val;
    }
    $params = $named;
}
return $this->statement->execute($params);';

$content = str_replace($old, $new, $content);
file_put_contents($file, $content);
echo "Patched LibSQLPDOStatement positional params\n";