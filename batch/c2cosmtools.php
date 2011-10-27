<?php

/*
 * Script used for manipulating c2c.osm
 * - see changes (based on what we can get from josm format)
 * - clean (remove deleted items, attributes new ids etc)
 * - extract kml
 */

libxml_use_internal_errors(true);

if ($argc < 3) usage();
switch ($argv[1]) {
  case 'diff':
    diff(); break;
  case 'clean':
    clean(); break;
  case 'extract':
    extractkml(); break;
  default:
    usage();
}

/*
 * Print to stdout a kml
 * extracted from josm file that can then
 * be imported into camptocamp
 */
function extractkml() {
  global $argc, $argv, $kmlname;

  if ($argc != 4) usage();

  $id = $argv[3];
  if (!preg_match('/^\d+$/', $id)) usage();

  $file =  $argv[2];
  if (file_exists($file)) {
    $xml = @simplexml_load_file($file);
  } else {
    file_not_found($file);
  }
  if ($xml === false) {
    bad_xml($file);
  }

  $nodes = $xml->node;
  foreach($nodes as $node) {
    $nt[(string) $node->attributes()->id] = array((float) $node->attributes()->lon, (float) $node->attributes()->lat);
  }

  $geoms = array();

  // find relations with the given id
  $relationmembers = array();
  $relations = $xml->relation;
  foreach ($relations as $relation) {
    $c2cid = c2cid($relation);
    if ($c2cid && in_array($id, $c2cid)) {
      kmlname($relation, $id);
      $relationid = (string) $relation->attributes()->id;
      $geoms[$relationid] = array();

      // we suppose we only have members of type way
      // but a way can be linked to more than one relation (and vice versa)
      $members = $relation->member;
      foreach ($members as $member) {
        $memberref = (string) $member->attributes()->ref;
        if (!isset($relationmembers[$memberref][$relationid])) {
          $relationmembers[$memberref][$relationid] = (string) $member->attributes()->role;
        }
      }
    }
  }

  $ways = $xml->way;
  foreach ($ways as $way) {
    // check that either way is a polygon of the c2c area
    $c2cid = c2cid($way);
    $wayid = (string) $way->attributes()->id;
    if ($c2cid && in_array($id, $c2cid)) {
      kmlname($way, $id);
      $geoms[] = get_nodes($way, $nt);
    }
    // or a border for a relation
    if (isset($relationmembers[$wayid])) {
      foreach ($relationmembers[$wayid] as $relationid => $role) {
        $geoms[$relationid][$role][] = get_nodes($way, $nt);
      }
    }
  }

  if (count($geoms)) {
    kmlstart();
    if (count($geoms) > 1) echo '    <MultiGeometry>', "\n";
    foreach ($geoms as $geom) {
      echo '      <Polygon>', "\n";
      if (!isset($geom['outer'])) {
        kmlboundary($geom);
      } else {
        kmlboundary($geom['outer'][0]);
        foreach ($geom['inner'] as $b) {
          kmlboundary($b, 'inner');
        }
      }
      echo '      </Polygon>', "\n";
    }
    if (count($geoms) > 1) echo '    </MultiGeometry>', "\n";
    kmlend();
  }
}

/* 
 * Given a file, we "clean" it:
 *  - deleted elements are removed
 *  - new elements (negative ids) are given proper ids
 * Result is sent to stdout
 */
function clean() {
  global $argc, $argv, $newid;

  if ($argc != 3) usage();
  $file =  $argv[2];
  if (file_exists($file)) {
    $file = @fopen($file, 'r');
  } else {
    file_not_found($file);
  }

  $newid = 1;
  $usedids= array();

  // assumption: we have a new line after each tag...
  if ($file) {
    while (($line = fgets($file)) !== false) {

      // node line
      if (strstr($line, '<node')) {
        if (!strstr($line, 'action=\'delete\'')) { // deleted node are simply discarded
          clean_line($line);
        }
        continue;
      }

      // way and relations
      $tags = array('way', 'relation');
      foreach ($tags as $tag) {
        if (strstr($line, '<' . $tag)) {
          if (!strstr($line, 'action=\'delete\'')) {
            clean_line($line);
          } else {
            // way or relation is deleted, we remove every line until we find the closing tag
            while (!strstr(fgets($file), '</' . $tag . '>')) {
              // discard the line
            }
          }
          continue 2;
        }
      }

      // nd and member tags
      if (strstr($line, '<nd') || strstr($line, '<member')) {
        echo clean_line($line, 'ref');
        continue;
      }

      // other kind of line
      echo $line;
    }
    if (!feof($file)) {
      die("Unexpected read fail. Aborting...\n");
    }
    fclose($file);
  }
}

/*
 * Output list of changes in a c2c/josm file
 * Possible changes in the file are:
 *  - action="delete" for relations, nodes or ways that have been deleted
 *  - action="modify" for relations, nodes or ways that have been modified
 *  - the use of a negative id is an indication of a creation
 * Also if the ways of a relation or the nodes of a way have been changed
 * the 'parents' are not marked as modified (yaaaaaaay)
 */
function diff() {
  global $argc, $argv;

  if ($argc != 3) usage();
  $file =  $argv[2];
  if (file_exists($file)) {
    $xml = @simplexml_load_file($file);
  } else {
    file_not_found($file);
  }
  if ($xml === false) {
    bad_xml($file);
  }
  echo "Analysing $file...\n";

  // Go through nodes and mark ways that have changed (we can easiky keep it in memory)
  // rq: use floats or strings but not ints, since PHP_INT_MAX is too small on 32 bits systems
  $impacted_ways = array();
  $nodes = $xml->node;
  foreach ($nodes as $node) {
    $nodeid = (string) $node->attributes()->id;
    $nodeaction = (string) $node->attributes()->action;

    if ((float) $nodeid < 0 || $nodeaction === 'modify') {
      $results = $xml->xpath("//way[count(nd[@ref='$nodeid'])>0]");
      if (count($results)) {
        foreach($results as $way) {
          $wayid = (string) $way->attributes()->id;
          if (!isset($impacted_ways[$wayid])) {
            $impacted_ways[$wayid] = 1;
          }
        }
      }
    }
  }

  // we go through ways, but only the ones that have a c2c:id attached
  // (other are part of a relation)
  $impacted_relations = array();
  $ways = $xml->way;
  foreach ($ways as $way) {
    // If only the nodes of a way have been modified or deleted, the way itself is not marked
    // as changed in josm. Let's check that.
    $wayid = (string) $way->attributes()->id;
    $propaged_modification = array_key_exists($wayid, $impacted_ways);

    $state = state($way);
    if ($propaged_modification && $state === 'NOCHANGE') $state = 'MODIFY';

    // check if it has a c2c:id attached
    // other ways are handled via their parent relation
    $c2cid = c2cid($way);
    if ($c2cid) {
      for ($i = 0; $i < count($c2cid); $i++) {
        $changes[$c2cid[$i]][$state][] = $wayid;
        if (!isset($changes[$c2cid[$i]]['name'])) $changes[$c2cid[$i]]['name'] = c2cname($way, $i);
      }
    } else {
      // determine if way has changed (either directly or via its nodes)
      // if so, keep track of the related relations that will be considered modified
      if ($state === 'MODIFY') {
        $results = $xml->xpath("//relation[count(member[@ref='$wayid'])>0]");
        if (count($results)) {
          foreach($results as $relation) {
            $relationid = (string) $relation->attributes()->id;
            if (!isset($impacted_relations[$relationid])) {
              $impacted_relations[$relationid] = 1;
            }
          }
        }
      }
    }
  }

  // we go through the relations
  $relations = $xml->relation;
  foreach ($relations as $relation) {
    $relationid = (string) $relation->attributes()->id;
    $propaged_modification = array_key_exists($relationid, $impacted_relations);

    $state = state($relation);
    if ($propaged_modification && $state === 'NOCHANGE') $state = 'MODIFY';

    $c2cid = c2cid($relation);
    if ($c2cid) {
      for ($i = 0; $i < count($c2cid); $i++) {
        $changes[$c2cid[$i]][$state][] = $relationid;
        if (!isset($changes[$c2cid[$i]]['name'])) $changes[$c2cid[$i]]['name'] = c2cname($relation, $i);
      }
    } else {
      die("Found a relation without c2c:id. Aborting...\n");
    }
  }

  // display the changes in a human readable way
  echo "\n";
  foreach ($changes as $id => $change) {
    $modifycount = isset($change['MODIFY']) ? count($change['MODIFY']) : 0;
    $deletecount = isset($change['DELETE']) ? count($change['DELETE']) : 0;
    $newcount = isset($change['NEW']) ? count($change['NEW']) : 0;
    $nochangecount = isset($change['NOCHANGE']) ? count($change['NOCHANGE']) : 0;

    // an area has been completely deleted
    if ($deletecount > 0 && $modifycount == 0 && $newcount == 0 && $nochangecount == 0) {
      echo $change['name'] . " ($id) has been deleted\n";
      continue;
    }

    // a new area has been created
    if ($newcount > 0 && $modifycount == 0 && $deletecount == 0 && $nochangecount == 0) {
      echo $change['name'] . " ($id) has been created with $newcount polygon(s)\n";
      continue;
    }

    // modified in various ways
    if ($newcount > 0 || $modifycount > 0 || $deletecount > 0) {
      echo $change['name'] . " ($id) has been changed\n";
      continue;
    }
  }
}


function clean_line($line, $attr='id') {
  global $newid, $usedids;

  // remove the action attribute
  $line = str_replace(' action=\'modify\'', '', $line);
  // change ids
  preg_match('/'.$attr.'=\'(-?\d+)/', $line, $matches);
  if (!isset($usedids[(string) $matches[1]])) {
    $nid = (string) $newid;
    $usedids[(string) $matches[1]] = $newid;
    $newid++;
  } else {
    $nid = $usedids[(string) $matches[1]];
  }
  $line = preg_replace('/'.$attr.'=\'-?\d+/', $attr.'=\'' . $nid, $line);
  if ($attr === 'id' && !strstr($line, 'version=')) {
    $line = str_replace('id=\'', 'version=\'1\' id=\'', $line);
  }
  echo $line;
}

function state($xmlelement) {
  $attribs = $xmlelement->attributes();
  if (isset($attribs->id) && (float) $attribs->id < 0) {
    return 'NEW';
  }
  if (isset($attribs->action)) {
    if ((string) $attribs->action === 'delete') {
      return 'DELETE';
    } else if ((string) $attribs->action === 'modify') {
      return 'MODIFY';
    }
  }
  return 'NOCHANGE';
}

// c2cid can be multiple (semicolon separated values)
function c2cid($xmlelement) {
  if (count($xmlelement->tag)) {
    foreach ($xmlelement->tag as $tag) {
      if ((string) $tag->attributes()->k === 'c2c:id') {
        return explode(';', (string) $tag->attributes()->v);
      }
    }
  }
  return false;
}

function c2cname($xmlelement, $i) {
  if (count($xmlelement->tag)) {
    foreach ($xmlelement->tag as $tag) {
      if ((string) $tag->attributes()->k === 'name') {
        $value = (string) $tag->attributes()->v;
        $names = explode(';', $value);
        return isset($names[$i]) ? $names[$i] : $value;
      }
    }
  }
  return false;
}

function file_not_found($file) {
  exit("Failed to open $file\n");
}

function bad_xml($file) {
  exit("Failed to properly parse $file\n");
}

function usage() {
  echo "Tool for manipulating c2c.osm file\n" .
       "Usage:\n" .
       "  See changes - php " . basename(__FILE__) . " diff <c2c.osm file>\n" .
       "  Clean file  - php " . basename(__FILE__) . " clean <c2c.osm file>\n" .
       "  Extract kml - php " . basename(__FILE__) . " extract <c2c.osm file> <c2cid>\n";
  exit;
}

function get_nodes($way, $nt) {
  $output = array();
  $nodes = $way->nd;
  foreach ($nodes as $node) {
    $ref = (string) $node->attributes()->ref;
    if (!isset($nt[$ref])) {
      die("Problem with the nodes. Aborting...\n");
    }
    $output[] = $nt[$ref];
  }
  return $output;
}

function kmlname($xmlelement, $c2cid) {
  global $kmlname;

  if (!isset($kmlname)) {
    if (count($xmlelement->tag)) {
      foreach ($xmlelement->tag as $tag) {
        if ((string) $tag->attributes()->k === 'name') {
          $c2cnames = explode(';', (string) $tag->attributes()->v);
        }
      }
    }
    $c2cids = c2cid($xmlelement);

    foreach ($c2cids as $k => $id) {
      if ($id == $c2cid) {
        $kmlname = $c2cnames[$k];
        return;
      }
    }
  }
}

function kmlstart() {
  global $kmlname;

  $kmlname = isset($kmlname) ? $kmlname : 'kmlname';
  echo '<?xml version="1.0" encoding="UTF-8"?>', "\n",
       '<kml xmlns="http://earth.google.com/kml/2.2">', "\n",
       '<Document>', "\n",
       '  <name><![CDATA[', $kmlname, ']]></name>', "\n",
       '  <Placemark id="1">', "\n",
       '    <name><![CDATA[', $kmlname, ']]></name>', "\n";
}

function kmlend() {
  echo '  </Placemark>', "\n",
       '</Document>', "\n",
       '</kml>';
}

function kmlboundary($nodes, $role = 'outer') {
  echo '        <', $role, 'BoundaryIs><LinearRing><tessellate>1</tessellate><coordinates>', "\n";
  foreach ($nodes as $node) {
    echo $node[0], ',', $node[1], "\n";
  }
  echo '        </coordinates></LinearRing></', $role, 'BoundaryIs>', "\n";
}
