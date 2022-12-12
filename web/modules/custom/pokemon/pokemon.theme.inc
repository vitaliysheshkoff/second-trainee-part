<?php

/**
 * @file
 * Pokemon custom theme hook preprocesses.
 */

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\media\Entity\Media;

/**
 * Implements template_preprocess_HOOK().
 */
function template_preprocess_pokemon_tax_term(&$variables) {
  /** @var \Drupal\taxonomy\TermInterface $taxonomy_term */
  $taxonomy_term = $variables['taxonomy_term'];
  $cache = CacheableMetadata::createFromRenderArray([
    '#context' => [
      'url.path',
    ],
  ]);

  $cache->addCacheableDependency($taxonomy_term);
  $item = [
    'id' => $taxonomy_term->id(),
    'weigh' => $taxonomy_term->getWeight(),
    'label' => $taxonomy_term->label(),
    'bundle' => $taxonomy_term->bundle(),
    'url' => $taxonomy_term->toUrl()->toString(TRUE)->getGeneratedUrl(),
    'view' => views_embed_view('pokemons', 'embed_2', $taxonomy_term->id()),
  ];

  $variables['item'] = $item;
  // Apply cache data to render array.
  $cache->applyTo($variables);
}

/**
 * Implements template_preprocess_HOOK().
 */
function template_preprocess_pokemon_node(&$variables) {
  /** @var \Drupal\node\Entity\Node $node ; */
  $node = $variables['node'];

  $variables['img_url'] = pokemon_getMediaImage($node, 'field_pokemon_image');

  $variables['id'] = $node->get('field_id')->getValue()[0]['value'];
  $variables['name'] = $node->get('field_name')->getValue()[0]['value'];
  $variables['height'] = $node->get('field_height')->getValue()[0]['value'];

  $tax_variables = [
    'color' => pokemon_getTerm($node, 'field_color'),
    'ability' => pokemon_getTerm($node, 'field_ability'),
    'habitat' => pokemon_getTerm($node, 'field_habitat'),
    'shape' => pokemon_getTerm($node, 'field_shape'),
    'specie' => pokemon_getTerm($node, 'field_specie'),
    'form' => pokemon_getTerm($node, 'field_form'),
    'type' => pokemon_getTerm($node, 'field_type'),
    'stat' => pokemon_getTerm($node, 'field_stat'),
    'egg_group' => pokemon_getTerm($node, 'field_egg_group'),
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
function pokemon_getTerm(\Drupal\node\Entity\Node $node, string $field_name) {
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
function pokemon_getMediaImage(\Drupal\node\Entity\Node $node, string $field_name) {
  if ($node->hasField($field_name)) {
    $mid = $node->get($field_name)->getValue()[0]['target_id'];
    $media = Media::load($mid);
    $fid = $media->getSource()->getSourceFieldValue($media);
    $file = \Drupal\file\Entity\File::load($fid);
    return $file->createFileUrl();
  }
}

