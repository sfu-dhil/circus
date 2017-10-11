(function () {
    var SCALE = 0.2;
    $(document).ready(function () {
        var $z = $('.zoomable');
        $z.find('.panzoom').each(function () {
            var $pz = $(this);
            var img = $pz.children('img')[0];
            console.log(img);
            $pz.panzoom({
                minScale: 0.1,
                maxScale: 2.0,
                rangeStep: 0.05,
                increment: 0.05,
            });
            $pz.panzoom("zoom", SCALE, {silent: true, focal: {clientX: $z.parent().width() * SCALE, clientY: $z.parent().height() * SCALE}});
            $pz.parent().on('mousewheel.focal', function (e) {
                e.preventDefault();
                var delta = e.delta || e.originalEvent.wheelDelta;
                var zoomOut = delta ? delta < 0 : e.originalEvent.deltaY > 0;
                $pz.panzoom('zoom', zoomOut, {
                    increment: 0.05,
                    animate: false,
                    focal: e
                });
            });
        });
    });
})();