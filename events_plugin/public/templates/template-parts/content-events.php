<article class="events">
    <?php the_post_thumbnail(); ?>
    <div class="events-content">
        <h2><?php the_title(); ?></h2>
            <div class="date__start">
                <p>Début de l'événement :</p>
                <?php the_field('date_start'); ?>
            </p>
            <div class="date__end">
                <p>Fin de l'événement :</p>
                <?php the_field('date_end'); ?>
            </p>
    </div>
</article>