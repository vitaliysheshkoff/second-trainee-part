<?php

/**
 * @file
 * Pokemon custom theme hook preprocesses.
 */

use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;

/**
 * Implements template_preprocess_HOOK().
 */
function template_preprocess_pokemon_node(&$variables) {
  /** @var \Drupal\node\Entity\Node $node ; */
  $node = $variables['node'];

  $variables['img_url'] = pokemon_get_media_image($node, 'field_pokemon_image');

  $variables['id'] = $node->get('field_id')->getValue()[0]['value'];
  $variables['name'] = $node->get('field_name')->getValue()[0]['value'];
  $variables['height'] = $node->get('field_height')->getValue()[0]['value'];

  $tax_variables = [
    'color' => pokemon_get_term($node, 'field_color'),
    'ability' => pokemon_get_term($node, 'field_ability'),
    'habitat' => pokemon_get_term($node, 'field_habitat'),
    'shape' => pokemon_get_term($node, 'field_shape'),
    'specie' => pokemon_get_term($node, 'field_specie'),
    'form' => pokemon_get_term($node, 'field_form'),
    'type' => pokemon_get_term($node, 'field_type'),
    'stat' => pokemon_get_term($node, 'field_stat'),
    'egg_group' => pokemon_get_term($node, 'field_egg_group'),
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
function pokemon_get_term(Node $node, string $field_name) {
  if ($node->hasField($field_name)) {
    // This will give the referenced terms on the node as objects.
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
function pokemon_get_media_image(Node $node, string $field_name) {
  if ($node->hasField($field_name)) {
    $mid = $node->get($field_name)->getValue()[0]['target_id'];
    $media = Media::load($mid);
    $fid = $media->getSource()->getSourceFieldValue($media);
    $file = File::load($fid);

    return $file->createFileUrl();
  }
}

