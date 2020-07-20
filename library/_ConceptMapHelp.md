---
"title": "Concept Mapping Help",
"tags": []
---
# Concept Mapping Help

This Wikitten based Concept Mapping tool is essentially a cross *hyper*-linked collection of concept entries ("postit notes"), together with a collection of automatically generated concept maps allowing the user to "fly over" the concepts contained in one or more of the collections of concept entries.

Each concept entry has some "meta-data" formated as a [JSON associative array](https://en.wikipedia.org/wiki/JSON). While editing a particular concept entry, the user can (and should) add either a `maps` or a `links` (or both) meta-data entries.

The `maps` meta-data entry is a list of the automatically generated concept maps into which this concept entry ("postit note") should be added. This provides a multi-dimensional way of categorizing concepts in the concept map.

The `links` meta-data entry is an associative array of link types (such as "see also", "depends on", "generalizes", "specializes", ... ) with which the current concept entry is meant to be cross-refferenced. Each of the link types is itself a list of *file paths* (with out the extension) of corresponding concept entries with which the current concept entry is to be linked.

This `maps` and `links` meta-data will be displayed to the reader as a collection of hyper-links at the top of the concept entry's description. Each of these hyper-links will take the reader to the corresponding overview map or concept entry.

The concept maps can be found in the `_maps` directory of the Wikitten ConceptMap. Each concept map can itself be cross-referenced using the `maps` and/or `links` meta-data entries. Each concept map can also contain any descriptive text the editor might want to write. However, the most important aspect of a concept map is an *interactive* concept map.

The [`theVortex`](_maps/theVortex.md) concept map, found in the `_maps` directory, is always (re)created. `theVortex` contains all concepts and associated links for the whole Concept Map.

Each node in the concept map represents one concept entry. The reader can determine which concept entry each node corresponds to by hovering with the mouse over a given node. Double clicking on any node will open up that concept entry in a new browser tab.

Each *directed* link in the concept map represents one cross reference between concept entries. Again, the reader can determine which type of cross reference a given link represents by hovering with the mouse over the given link.

Individual nodes in an interactive map can be pulled and pushed by grabbing the node with the mouse. This allows the reader to "see" how the associated links progress from a given concept to all other connected concepts.

Individual nodes can also be high-lighted by simply clicking on a give node. This allows the reader to select a number of important concepts and then see how they inter-relate as the reader pulls nodes around. (A second click will remove the high-lighting).

Any concept map can be "zoomed" in and out using the mouse wheel. Any concept map can be repositioned (as a unit) by grabbing the concept map "paper" outside of any nodes and links.

The [`_LinkAttributes`](_LinkAttributes.md) Wikitten entry can be used to specify Link Attributes for each type of cross-reference the editor might like to provide attributes for. (At the moment the only link attribute is "color" and the default "color" is "black" whenever no link attributes can be found associated with a given link type.)

## To Do

- make a mv command (command line?) to move topics around between directories. It will be very likely that the original place a topic placed is wrong in the longer term, so a tool which moves the file and then updates all links would be useful.
