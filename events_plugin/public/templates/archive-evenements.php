<?php

get_header();

$terms = get_terms( array(
    'taxonomy' => 'type_evenement',
    'orderby' => 'name',
) );
?>

<div class="container">
    <div class="row flex-grow-1">
        <div class="col-12 col-sm-6 col-lg-2 events__filters">
            <div class="row">
                <div class="filters__toggle">
                    Filtrer
                    <div class="toggle__icon">
                        <div class="bar"></div>
                        <div class="bar"></div>
                    </div>
                </div>
                <div id="events__filters-container" class="filters__wrapper">
                    <ul>
                        <li class="filters__item">
                            <a href="#" class="active">Tous les événements</a>
                        </li>
                        <?php foreach ($terms as $term) {?>
                            <li class="filters__item">
                                <a href="#" data-slug="<?php echo $term->slug; ?>">
                                    <?php echo $term->name; ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-10 d-flex flex-column">
            <div id="events__wrapper" class="row flex-grow-1 events__wrapper">
                <?php
                if ( have_posts() ) {
                    while ( have_posts() ) {
                        the_post();
                        require( plugin_dir_path( __FILE__ ) . 'template-parts/content-events.php');
                    }
                    if ( get_next_posts_link() ) { ?>
                        <button class="events__loadmore">Voir plus</button>
                        <?php
                    }
                } ?>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();