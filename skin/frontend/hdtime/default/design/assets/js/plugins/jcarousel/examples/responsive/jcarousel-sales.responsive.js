(function($) {
    $(function() {
        var jcarousel = $('#carousel-sales');

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
            })
            .jcarousel({
                wrap: 'circular'
            });

        $('.sales-control-prev')
            .jcarouselControl({
                target: '-=1'
            });

        $('.sales-control-next')
            .jcarouselControl({
                target: '+=1'
            });

        $('.sales-pagination')
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
