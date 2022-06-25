$("#sidebar-price-slider").slider({
  orientation: "horizontal",
  range: true,
  values: [0, 35],
  min: 0,
  max: 35,
  step: 0.25,
});

$("#sidebar-price-slider").on("slide", function (event, ui) {
  $("#input-price-min").val(ui.values[0]);
  $("#input-price-max").val(ui.values[1]);
});

$("#input-price-min").val($("#sidebar-price-slider").slider("values", 0));
$("#input-price-min").data("val", $("#sidebar-price-slider").slider("values", 0));

$("#input-price-max").val($("#sidebar-price-slider").slider("values", 1));
$("#input-price-max").data("val", $("#sidebar-price-slider").slider("values", 1));

$("#input-price-min")
  .on("input", function (e) {
    if ($(this).val() <= $("#sidebar-price-slider").slider("values", 1) && $(this).val() >= $("#sidebar-price-slider").slider("option", "min")) {
      $("#sidebar-price-slider").slider("values", 0, $("#input-price-min").val());
      $(this).data("val", $(this).val());
    }
  })
  .on("blur", function (e) {
    if ($(this).val() <= $("#sidebar-price-slider").slider("values", 1) && $(this).val() >= $("#sidebar-price-slider").slider("option", "min")) {
      $("#sidebar-price-slider").slider("values", 0, $("#input-price-min").val());
      $(this).data("val", $(this).val());
    } else {
      $(this).val($(this).data("val"));
    }
  });

$("#input-price-max")
  .on("input", function (e) {
    if ($(this).val() >= $("#sidebar-price-slider").slider("values", 0) && $(this).val() <= $("#sidebar-price-slider").slider("option", "max")) {
      $("#sidebar-price-slider").slider("values", 1, $("#input-price-max").val());
      $(this).data("val", $(this).val());
    }
  })
  .on("blur", function (e) {
    if ($(this).val() >= $("#sidebar-price-slider").slider("values", 0) && $(this).val() <= $("#sidebar-price-slider").slider("option", "max")) {
      $("#sidebar-price-slider").slider("values", 1, $("#input-price-max").val());
      $(this).data("val", $(this).val());
    } else {
      $(this).val($(this).data("val"));
    }
  });

$(".sidebar-filter-header").on("click", function (e) {
  $(this).parent().toggleClass("filter-collapsed");
});

$(document).ready(function () {
  $("#recommendation").owlCarousel({
    loop: true,
    autoplay: true,
    margin: 10,
    animateOut: "fadeOut",
    animateIn: "fadeIn",
    nav: true,
    dots: true,
    autoplayHoverPause: true,
    items: 3,
    navText: ["<i class='fas fa-chevron-left fa-lg dark-pink'></i>", "<i class='fas fa-chevron-right fa-lg dark-pink'></i>"],
    responsiveClass: true,
    responsive: {
      0: {
        items: 1,
      },
      576: {
        items: 2,
      },
      768: {
        items: 3,
      },
      992: {
        items: 4,
      },
      1200: {
        items: 5,
      },
    },
  });

  $("#product-images").owlCarousel({
    loop: true,
    center: true,
    margin: 15,
    animateOut: "fadeOut",
    animateIn: "fadeIn",
    nav: true,
    dots: true,
    items: 3,
    navText: ["<i class='fas fa-chevron-left fa-lg dark-pink'></i>", "<i class='fas fa-chevron-right fa-lg dark-pink'></i>"],
  });
});

$("#navbar-mobile li>i").click(function () {
  $(this).toggleClass("rotate-180");
  $(this).parent().siblings().find("ul").slideUp(300);
  $(this).next("ul").stop(true, false, true).slideToggle(300);
  return false;
});
