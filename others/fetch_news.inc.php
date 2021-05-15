<?php
function fetch_news($label, $url, $storage_url){
    if ($label === 'all') {
        $news_query = wp_remote_post($url, [
            'headers' => [
                    'Content-Type' => 'application/json'
            ],
            'body' => wp_json_encode([
                    'query' => '
                {
                    public_actualities(
                        first: 1000
                        orderBy: [{ column: PUBLISHED_AT, order: DESC }]
                        in_progress: true
                    ) {
                        edges {
                          node {
                            id
                            content
                            image
                            images
                            date_event
                            published_at
                            finish_at
                            url
                            active
                            shop_id
                            created_at
                            updated_at
                            program_id
                            content_long
                            shop {
                              id
                              shop_name
                              thematics {
                                edges {
                                  node {
                                    id
                                    label
                                    svg
                                  }
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
    }else{
        $news_query = wp_remote_post($url, [
            'headers' => [
                    'Content-Type' => 'application/json'
            ],
            'body' => wp_json_encode([
                    'query' => '
                {
                    public_actualities(
                        first: 1000
                        orderBy: [{ column: PUBLISHED_AT, order: DESC }]
                        filters: {inclusif: true, filters:[{ column: THEMATIC_LABEL, operator: EQUAL, value: "' . $label . '"}]}
                        in_progress: true
                        ) {
                        edges {
                          node {
                            id
                            content
                            image
                            images
                            date_event
                            published_at
                            finish_at
                            url
                            active
                            shop_id
                            created_at
                            updated_at
                            program_id
                            content_long
                            shop {
                              id
                              shop_name
                              thematics {
                                edges {
                                  node {
                                    id
                                    label
                                    svg
                                  }
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
    }
    $news_decoded_response = json_decode($news_query['body'], true);
    $news = $news_decoded_response['data']['public_actualities']['edges'];
    if (!empty($news)) {
        $count = 0;
        foreach ($news as $actuality) {
            $actuality_content = $actuality['node'];
            $active = $actuality_content['active'];
            $id = $actuality_content['id'];
            $title = $actuality_content['content'];
            $image = $actuality_content['image'];
            $date = $actuality_content['published_at'];
            $date = strtotime($date);
            $date = date("d-m-Y", $date);
            $date = str_replace("-", "/", $date);
            $image = $actuality_content['image'];
            $content = $actuality_content['content_long'];
            if (!is_null($actuality_content['shop'])) {
                $shop_name = $actuality_content['shop']['shop_name'];
                $shop_ID = $actuality_content['shop']['id'];
                $thematics_ID = $actuality_content['shop']['thematics']['edges'][0]['node']['id'];
                $thematics = $actuality_content['shop']['thematics']['edges'][0]['node']['label'];
                $thematics_icon = $actuality_content['shop']['thematics']['edges'][0]['node']['svg'];
            }
            if ($active === true) {
                if ($count === 0) { ?>
                    <article class="news featured col">
                        <a class="news-inner <?php echo $thematics ?> d-flex fancyboxCustom" href="?id=<?php echo $id; ?>">
                            <div class="news-image">
                                <img src="<?php echo $storage_url . $image ?>" alt="image <?php echo $title ?>">
                            </div>

                            <div class="d-flex flex-column justify-content-between news-content">
                                <h2><?php echo $title; ?></h2>
                                <div class="news-details flex-wrap">
                                    <p class="author">
                                        <?php if (empty($shop_name)) {
                                            echo get_bloginfo('name');
                                        } else {
                                            echo $thematics_icon . $shop_name;
                                        } ?>
                                    </p>
                                    <p class="date w-100">
                                        <i class="icon-FW_News"></i> <?php echo $date ?>
                                    </p>
                                </div>
                                <p class="news-excerpt">
                                    <?php echo $content; ?>
                                </p>
                            </div>
                        </a>
                    </article>
                <?php }
                if ($count > 0) { ?>
                    <article class="news col">
                        <div class="news-inner <?php echo $thematics ?>">
                            <a class="fancyboxCustom" href="?id=<?php echo $id; ?>">
                                <img src="<?php echo $storage_url . $image ?>" alt="image <?php echo $title ?>">
                                <div class="d-flex flex-column justify-content-between news-content">
                                    <div class="news-content__wrapper">
                                        <h2><?php echo $title; ?></h2>
                                        <p class="news-excerpt"><?php echo $content; ?></p>
                                    </div>
                                    <div class="news-details">
                                        <p class="author">
                                            <i class="icon-thematique-<?php echo $thematics_ID ?>"></i>
                                            <?php if (empty($shop_name)) {
                                                echo get_bloginfo('name');
                                            } else {
                                                echo $thematics_icon . $shop_name;
                                            } ?>
                                        </p>
                                        <p class="date">
                                            <i class="icon-FW_News"></i> <?php echo $date ?>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </article>
                <?php }
                $count++;
            }
        }
    } else { ?>
    <div class="col">
        <div id="not-found" class="news__not-found archive__not-found">
            <div id="not-found" class="not-found__wrapper">
                <div class="img-wrapper">
                    <img src="/wp-content/themes/fidwell/assets/img/icon-404.png" alt="404">
                </div>
                <div class="text-wrapper">
                    <p><?php _e('Aucune actualité n\'a été trouvée'); ?></p>
                </div>
                <div class="w-100"></div>
                <div class="cta-wrapper">
                    <div class="cta-wrapper__inner">
                        <a href="<?php home_url(); ?>" class="cta with-icon refresh"><?php _e('Voir toutes les actualités'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php }
}
