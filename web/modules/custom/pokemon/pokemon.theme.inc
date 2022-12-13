<?php

/**
 * @file
 * Pokemon custom theme hook preprocesses.
 */

use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;

/**
 * Implements template_preprocess_HOOK().
 */
function template_preprocess_pokemon_node(&$variables) {
  /** @var \Drupal\node\Entity\Node $node ; */
  $node = $variables['node'];

  $variables['img_url'] = pokemonGetMediaImage($node, 'field_pokemon_image');

  $variables['id'] = $node->get('field_id')->getValue()[0]['value'];
  $variables['name'] = $node->get('field_name')->getValue()[0]['value'];
  $variables['height'] = $node->get('field_height')->getValue()[0]['value'];

  $tax_variables = [
    'color' => pokemonGetTerm($node, 'field_color'),
    'ability' => pokemonGetTerm($node, 'field_ability'),
    'habitat' => pokemonGetTerm($node, 'field_habitat'),
    'shape' => pokemonGetTerm($node, 'field_shape'),
    'specie' => pokemonGetTerm($node, 'field_specie'),
    'form' => pokemonGetTerm($node, 'field_form'),
    'type' => pokemonGetTerm($node, 'field_type'),
    'stat' => pokemonGetTerm($node, 'field_stat'),
    'egg_group' => pokemonGetTerm($node, 'field_egg_group'),
  ];

  $variables['tax_variables'] = $tax_variables;
}

/**
 * Gets term from node.
 *
 * @param \Drupal\node\Entity\Node $node
 *   The node.
 * @param string $field_name
 *   The node field name.
 *
 * @return array|void
 *   The taxonomy field term.
 */
function pokemonGetTerm(Node $node, string $field_name) {
  if ($node->hasField($field_name)) {
    //this will give the referenced terms on the node as objects
    $term_objects = $node->get($field_name)->referencedEntities();
    $terms = [];
    foreach ($term_objects as $term_object) {
      $terms[] = [
        'value' => $term_object->label(),
        'url' => $term_object->toUrl()->toString(TRUE)->getGeneratedUrl(),
      ];
    }

    return $terms;
  }
}

/**
 * Gets image from the node.
 *
 * @param \Drupal\node\Entity\Node $node
 *   The node.
 * @param string $field_name
 *   The node field name.
 *
 * @return string|void
 *   The media field image.
 */
function pokemonGetMediaImage(Node $node, string $field_name) {
  if ($node->hasField($field_name)) {
    $mid = $node->get($field_name)->getValue()[0]['target_id'];
    $media = Media::load($mid);
    $fid = $media->getSource()->getSourceFieldValue($media);
    $file = \Drupal\file\Entity\File::load($fid);

    return $file->createFileUrl();
  }
}

