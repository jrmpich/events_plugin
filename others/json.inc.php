<?php
/**
 * Accès à la BDD Wordpress
 */
require_once('../../../../../wp-load.php');
global $wpdb;

$blog_id = get_current_blog_id();
if ($blog_id != 1) {
    $options_table = "wp_".$blog_id."_options";
}
else {
    $options_table = "wp_options";
}

/**
 * Récupération liste des commerces
 */
$url = $wpdb->get_results(
    "
        SELECT option_value
        FROM ".$options_table."
        WHERE option_name = 'options_api_url'"
);
$url = $url[0]->option_value;
$url_storage = $wpdb->get_results(
    "
        SELECT option_value
        FROM ".$options_table."
        WHERE option_name = 'options_storage_url'"
);
$url_storage = $url_storage[0]->option_value;

$request = wp_remote_post( $url, [
    'headers' => [
        'Content-Type' => 'application/json'
    ],
    'body' => wp_json_encode([
        'query' => '
            {
              public_shops(first: 1000) {
                edges {
                  node {
                    id
                    shop_name
                    description
                    address
                    phone
                    latitude
                    longitude
                    image_banner
                    images
                    google_data {
                        business_status
                        is_open_now
                        monday
                        tuesday
                        wednesday
                        thursday
                        friday
                        saturday
                        sunday
                        rating
                        user_ratings_total
                    }
                    thematics {
                      edges {
                        node {
                          id
                          label
                          svg
                        }
                      }
                    }
                    actualities {
                      edges {
                        node {
                          id
                          content
                          image
                          content_long
                        }
                      }
                    }
                    rules {
                      edges {
                        node {
                          percentage
                          higher_amount
                          lower_amount
                          begin_at
                          finish_at
                        }
                      }
                    }
                    exceptional_rules {
                      edges {
                        node {
                          rules {
                            edges {
                              node {
                                percentage
                                higher_amount
                                lower_amount
                                begin_at
                                finish_at
                              }
                            }
                          }
                        }
                      }
                    }
                    flash_sales {
                      edges {
                        node {
                          title
                          content
                          finish_at
                        }
                      }
                    }
                  }
                }
              }
            }
        '
    ])
]);

$decoded_response = json_decode( $request['body'], true );
$commerces = $decoded_response['data']['public_shops']['edges'];

$today = date('w') - 1;

if (is_array($commerces)) {
    foreach ($commerces as $commerce) {
        $commerce = $commerce['node'];
        $image = $commerce['image_banner'];
        $image_url = "";
        if (!is_null($image)) {
            $image_url = $url_storage.$image;
            $image_url = str_replace("\"", "", $image_url);
        }
        /**
         * Récupération des thématiques
         */
        $thematique = $commerce['thematics']['edges'][0]['node']['label'];
        $thematique = explode(" ", $thematique)[0];
        $thematique_id = $commerce['thematics']['edges'][0]['node']['id'];

        $thematique_icon = "";
        if (!is_null($commerce['thematics']['edges'][0]['node']['svg'])) {
            $thematique_icon = $commerce['thematics']['edges'][0]['node']['svg'];
        }

        /**
         * Récupération des horaires d'ouverture
         */
        if ($commerce['google_data']['is_open_now'] == true) {
            $status = 'Ouvert';
        }
        else {
            $status = 'Fermé';
        }
        $monday = $commerce['google_data']['monday'];
        $tuesday = $commerce['google_data']['tuesday'];
        $wednesday = $commerce['google_data']['wednesday'];
        $thursday = $commerce['google_data']['thursday'];
        $friday = $commerce['google_data']['friday'];
        $saturday = $commerce['google_data']['saturday'];
        $sunday = $commerce['google_data']['sunday'];
        $openTime = array();
        array_push($openTime, $monday, $tuesday, $wednesday, $thursday, $friday, $saturday, $sunday);

        /**
         * Récupération des règles statiques
         */
        $rules = $commerce['rules']['edges'];
        $regle_max = "";
        $regles = array();
        if (is_array($rules)) {
            foreach ($rules as $rule) {

                $rule = $rule['node'];
                array_push($regles, $rule);
                if (empty($regle_max)) {
                    $regle_max = $rule['percentage'];
                }
                elseif (!empty($regle_max) && $regle_max < $rule['percentage']){
                    $regle_max = $rule['percentage'];
                }
            }
        }

        $entities[] = [
            'id' => $commerce['id'],
            'name' => $commerce['shop_name'],
            'longitude' => $commerce['longitude'],
            'latitude' => $commerce['latitude'],
            'address' => $commerce['address'],
            'phone' => $commerce['phone'],
            'schedule' => $status,
            'todaySchedule' => $openTime[$today],
            'type' => $thematique,
            'type_id' => $thematique_id,
            'type_icon' => $thematique_icon,
            'label' => $thematique,
            'regles' => $regles,
            'regle_max' => $regle_max,
            'promo' => '',
            'image' => $image_url
        ];
    }
}

$json = json_encode($entities);
echo $json;