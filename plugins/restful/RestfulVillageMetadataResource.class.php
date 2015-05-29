<?php

class RestfulVillageMetadataResource extends RestfulEntityBaseNode {

  /**
   * Overrides RestfulEntityBaseNode::publicFieldsInfo().
   */
  public function publicFieldsInfo() {
    $public_fields = parent::publicFieldsInfo();

    $public_fields['founder'] = array(
      'property' => 'field_village_founder',
    );

    $public_fields['senechal'] = array(
      'property' => 'field_village_senechal',
    );

    $public_fields['date_founded'] = array(
      'property' => 'field_village_founded',
    );

    $public_fields['name'] = array(
      'property' => 'field_village_name',
    );

    $public_fields['notes'] = array(
      'property' => 'field_village_notes',
    );

    $public_fields['market_square'] = array(
      'property' => 'field_village_market_square',
    );

    $public_fields['site_plan'] = array(
      'property' => 'field_village_plan_and_site',
    );

    $public_fields['special_features'] = array(
      'property' => 'field_village_special_features',
    );

    $public_fields['location'] = array(
      'callback' => 'static::location',
    );

    $public_fields['photo_years'] = array(
      'callback' => 'static::photo_years',
    );

    return $public_fields;
  }

  public static function photo_years($wrapper) {
    // find the nids of image_metadata nodes for this village
    $nid = $wrapper->nid->value();
    $efq = new EntityFieldQuery();
    $result = $efq
      ->entityCondition('entity_type', 'node')
      ->entityCondition('bundle', 'image_metadata')
      ->fieldCondition('field_village_reference', 'target_id', $nid, '=')
      ->fieldOrderBy('field_photo_year', 'value', 'ASC')
      ->addMetaData('account', user_load(1))
      ->execute();

    if (isset($result['node'])) {
      $images = $result['node'];

      // At first we need to get field's id. If you already know field id, you can ommit this step
      // Get all fields attached to a given node type
      $fields = field_info_instances('node', 'image_metadata');

      // Get id of body field
      $photo_year_field_id = $fields['field_photo_year']['field_id'];

      // Attach a field of selected id only to get value for it
      field_attach_load('node', $images, FIELD_LOAD_CURRENT, array('field_id' => $photo_year_field_id));

      $known_year = array();
      foreach ($images as $nid => $node) {
        $year = $node->field_photo_year[LANGUAGE_NONE][0]['value'];
        $known_year["$year"] = 1;
      }
      // get values of our field
      $years = array_keys($known_year);
    }
    else {
      $years = array();
    }

   return $years;
  }

  public static function location($wrapper) {
    $loc = $wrapper->field_media_location->value();
    if (empty($loc)) {
      return array();
    }
    return array('lat' => $loc['lat'], 'lon' => $loc['lon'], 'latlon' => array($loc['lat'],$loc['lon']));
  }

}
