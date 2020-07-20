---
"title": "Link Attribute Mapping",
"tags": [],
"linkMapping": {
  "see also": { "color": "red" },
  "depends on": { "color": "blue" }
}
---
# Link Attribute Mapping

This wikitten entry contains the Link Attribute Mapping used by the "updateConceptMaps" process.

The "updateConceptMaps" process expects a JSON entry named "linkMapping" in the "meta-data" for this entry.

The "linkMapping" must be a JSON associative array whose "linkType"=>"attributes" pairs define the attribute mapping for each link type. The attributes associated with each link type, is itself an associative array of "attributeName"=>"attributeValue" pairs.
