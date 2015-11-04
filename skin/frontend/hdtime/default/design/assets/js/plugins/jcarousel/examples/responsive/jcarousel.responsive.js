(function($) {
    $(function() {
        var jcarousel = $('#carousel-new');

        jcarousel
            .on('jcarousel:reload jcarousel:create', function () {
                var width = jcarousel.innerWidth();

                if (width >= 768) {
                    width = width / 6;
                } else if (width >= 350) {
                    width = width / 4;
                } else {
                  width = width / 2;
                }
                
                width = 140;

                jcarousel.jcarousel('items').css('width', width + 'px');
                $('.jcarousel-control-prev')
                    .jcarouselControl({
                        target: '-=1'
                    });

                $('.jcarousel-control-next')
                    .jcarouselControl({
                        target: '+=1'
                    });
            })
            .jcarousel({
                wrap: 'circular'
            });



        $('.jcarousel-pagination')
            .on('jcarouselpagination:active', 'a', function() {
                $(this).addClass('active');
            })
            .on('jcarouselpagination:inactive', 'a', function() {
                $(this).removeClass('active');
            })
            .on('click', function(e) {
                e.preventDefault();
            })
            .jcarouselPagination({
                perPage: 1,
                item: function(page) {
                    return '<a href="#' + page + '">' + page + '</a>';
                }
            });
    });
})($j);
