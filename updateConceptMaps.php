<?php

function extractJsonFrontMatter($source) {
  static $front_matter_regex = "/^---[\r\n](.*)[\r\n]---[\r\n](.*)/s";

  $source = ltrim($source);
  $meta_data = array();

  if (preg_match($front_matter_regex, $source, $matches)) {
    $json = trim($matches[1]);
    $source = trim($matches[2]);

    // Locate or append starting and ending brackets,
    // if necessary. I lazily only check the first
    // character for a bracket, so that it'll work
    // even if the user includes a hash in the last
    // line:
    if ($json[0] != '{') {
      $json = '{' . $json . '}';
    }

    // Decode & validate the JSON payload:
    $meta_data = json_decode($json, true, 512);

    // Check for errors:
    if ($meta_data === null) {
      $error = json_last_error();
      $message = 'There was an error parsing the JSON Front Matter for this page';

      // todo: Better error information?
      if ($error == JSON_ERROR_SYNTAX) {
        $message .= ': Incorrect JSON syntax (missing comma, or double-quotes?)';
      }

      throw new RuntimeException($message);
    }
  }

  // ensure the 'theVortex' map is in all meta_data['maps'] arrays.
  //  
  if (array_key_exists('maps', $meta_data)) { } else { $meta_data['maps'] = [ ]; }
  if (in_array('theVortex', $meta_data['maps'])) {
  } else {
    array_push($meta_data['maps'], 'theVortex');
  }

  return array($source, $meta_data);
}

function getTree($dir, $files = []) {

  $items = scandir($dir);
  foreach ($items as $item) {
    if (preg_match("/^\..*|^CVS$|\.json$/", $item)) {
      continue;
    }

    $path = $dir . DIRECTORY_SEPARATOR . $item;
    if (is_dir($path)) {
      $files = getTree($path, $files);
      continue;
    }
    array_push($files, $path);
  }

  uksort($files, "strnatcasecmp");

  return $files;
}

echo "\n***\n";
$timeNow = new DateTime('now', new DateTimezone('Europe/London'));
echo "* update of conceptMaps started at " . $timeNow->format('Y/m/d H:i:s') . "\n";

if ($argc < 2) {
  echo "usage: updateConceptMaps <<library directory>>\n";
  exit(-1);
}

$libDir = $argv[1];

$files = getTree($libDir);

$libDir       = $libDir . DIRECTORY_SEPARATOR;
$libDirRegExp = preg_replace("/\//", "\\\/", $libDir);

$maps = [ ];

foreach ($files as $aFilePath) {
  echo "* working on: [" . $aFilePath . "]\n";
  $source   = file_get_contents($aFilePath);

  $thisPage = preg_replace("/^" . $libDirRegExp . "/", "", $aFilePath);
  $thisPage = preg_replace("/\..*$/", "", $thisPage);
  list($source, $meta_data) = extractJsonFrontMatter($source);
//  echo "<pre>".print_r($meta_data)."</pre>\n";
  if (array_key_exists('maps', $meta_data)) {
    foreach ($meta_data['maps'] as $aMapKey) {
      if (array_key_exists($aMapKey, $maps)) { } else { $maps[$aMapKey] = [ ]; }
      if (array_key_exists('links', $meta_data)) {
        $maps[$aMapKey][$thisPage] = $meta_data['links'];
      } else {
        $maps[$aMapKey][$thisPage] = [ ];
      }
    }
  }
}

$defaultLinkAttributes = [ "color" => "black" ];
$linkAttributes = [ ];
$linkAttrFile = $libDir . '_LinkAttributes.md';
if (file_exists($linkAttrFile)) {
  $source   = file_get_contents($linkAttrFile);
  list($source, $meta_data) = extractJsonFrontMatter($source);
  if (array_key_exists('linkMapping', $meta_data)) {
    $linkAttributes = $meta_data['linkMapping'];
    echo "* loaded link attributes from: [$linkAttrFile]\n";
  }
}

foreach ($maps as $mapName => $mapStructure) {
  echo "* creating map [$mapName]\n";
  $nodes = [];
  $links = [];
  foreach ($mapStructure as $aNode => $someLinks) {
    if (!in_array($aNode, $nodes)) {
      array_push($nodes, $aNode);
    }
    foreach ($someLinks as $linkType => $someLinkingNodes) {
      foreach ($someLinkingNodes as $aLinkNode) {
        if (!in_array($aLinkNode, $nodes)) {
          array_push($nodes, $aLinkNode);
        }
        $aJsonLink = [ $aNode, $aLinkNode, $linkType ];
        if (!in_array($aJsonLink, $links)) {
          array_push($links, $aJsonLink);
        }
      }
    }
  }
  $jsonPath = $libDir . '_maps' . DIRECTORY_SEPARATOR . $mapName . '.json';
  echo "* writing map data to: [" . $jsonPath . "]\n";
  $mapFile = fopen($jsonPath, 'w');
  fwrite($mapFile, "{\n");
  fwrite($mapFile, "  \"nodes\": [\n");
  foreach ($nodes as $aNode) {
    fwrite($mapFile, "    { \"id\": \"$aNode\", \"path\": \"$aNode.md\" },\n");
  }
  fwrite($mapFile, "  ],\n");
  fwrite($mapFile, "  \"links\": [\n");
  foreach ($links as $aLink) {
    fwrite($mapFile, "    {");
    fwrite($mapFile, " \"source\": \"$aLink[0]\"");
    fwrite($mapFile, ", \"target\": \"$aLink[1]\"");
    fwrite($mapFile, ", \"linkType\": \"$aLink[2]\"");
    $theLinkAttrs = $defaultLinkAttributes;
    if (array_key_exists($aLink[2], $linkAttributes)) {
      $theLinkAttrs = $linkAttributes[$aLink[2]];
    }
    foreach ($theLinkAttrs as $aKey => $aValue) {
      fwrite($mapFile, ", \"$aKey\": \"$aValue\"");
    }
    fwrite($mapFile, " },\n");
  }
  fwrite($mapFile, "  ],\n");
  fwrite($mapFile, "}\n");
  fclose($mapFile);

  $mdPath = preg_replace("/\.json$/", ".md", $jsonPath);
  if (!file_exists($mdPath)) {
    echo "* creating new map topic file as: [" . $mdPath . "]\n";
    $mdFile = fopen($mdPath, 'w');
    fwrite($mdFile, "---\n");
    fwrite($mdFile, "\"title\": \"New topic\",\n");
    fwrite($mdFile, "\"tags\": []\n");
    fwrite($mdFile, "---\n");
    fwrite($mdFile, "# New topic\n");
    fclose($mdFile);
  }
}

$timeNow = new DateTime('now', new DateTimezone('Europe/London'));
echo "* update of conceptMaps finished at " . $timeNow->format('Y/m/d H:i:s') 
. "\n";
echo "***\n";

?>
