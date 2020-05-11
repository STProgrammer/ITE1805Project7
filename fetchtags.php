<?php

$stmt = $db->prepare("select tag from Tags order by tag;");

$stmt->execute();

$tags = $stmt->fetchAll(PDO::FETCH_COLUMN);

$jsonData = json_encode($tags);

file_put_contents($rel.'tags.json', $jsonData);

?>