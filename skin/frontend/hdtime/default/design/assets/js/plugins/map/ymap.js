
var loc;
var shir = 55.774558;
var dolg = 37.62222;

ymaps.ready(init);

function init(){
  loc = new ymaps.Map ("loc", {
      center: [shir,dolg],
      zoom:17,
      });
  loc.controls.add('zoomControl');
  loc.controls.add('typeSelector');
  loc.controls.add(new ymaps.control.MapTools());
  loc.controls.add(new ymaps.control.ScaleLine());

  mark = new ymaps.Placemark([shir,dolg], {
    balloonContent: '<p>г. Москва, ул. Троицкая, д.8</p>',
    balloonContentHeader: '<h3>АЛЕКАТ</h3>',
    balloonContentFooter: 'тел. 8 (495) 669 15 25,',
    balloonContentBody: ''
    });
    loc.geoObjects.add(mark);
}
