$("#modal").modal("show");

$("#sidebar-price-slider").slider({
    orientation: "horizontal",
    range: true,
    values: [
        parseFloat($("#input-price-min").val()),
        parseFloat($("#input-price-max").val()),
    ],
    min: parseFloat($("#input-price-min").attr("initial-value")),
    max: parseFloat($("#input-price-max").attr("initial-value")) + 0.001,
    step: 0.01,
});

$("#filter-form").submit(function (event) {
    event.preventDefault();
    var values = {};
    $(this)
        .find('input[type="checkbox"]:checked')
        .each(function () {
            if (this.name in values) {
                values[this.name].push(this.value);
            } else {
                values[this.name] = [this.value];
            }
        });

    $(this)
        .find(".price-min input, .price-max input")
        .each(function () {
            if (this.getAttribute("initial-value") != this.value) {
                values[this.name] = [this.value];
            }
        });

    const params = new URLSearchParams();
    params.delete("price-min");
    params.delete("price-max");

    Object.keys(values).forEach((key) => {
        params.set(key, values[key].join(","));
    });

    window.location.search = params.toString();
});

$("#order-form").submit(function (event) {
    event.preventDefault();
    const sort = $(this).find("option:selected").val();

    const params = new URLSearchParams(window.location.search);
    if (sort != "bestsell") params.set("sort", sort);
    else params.delete("sort");
    window.location.search = params.toString();
});

$("#sidebar-price-slider").on("slide", function (event, ui) {
    $("#input-price-min").val(ui.values[0]);
    $("#input-price-max").val(ui.values[1]);
});

$("#input-price-min").val($("#sidebar-price-slider").slider("values", 0));
$("#input-price-min").data(
    "val",
    $("#sidebar-price-slider").slider("values", 0)
);

$("#input-price-max").val($("#sidebar-price-slider").slider("values", 1));
$("#input-price-max").data(
    "val",
    $("#sidebar-price-slider").slider("values", 1)
);

$("#input-price-min")
    .on("input", function (e) {
        if (
            $(this).val() <= $("#sidebar-price-slider").slider("values", 1) &&
            $(this).val() >= $("#sidebar-price-slider").slider("option", "min")
        ) {
            $("#sidebar-price-slider").slider(
                "values",
                0,
                $("#input-price-min").val()
            );
            $(this).data("val", $(this).val());
        }
    })
    .on("blur", function (e) {
        if (
            $(this).val() <= $("#sidebar-price-slider").slider("values", 1) &&
            $(this).val() >= $("#sidebar-price-slider").slider("option", "min")
        ) {
            $("#sidebar-price-slider").slider(
                "values",
                0,
                $("#input-price-min").val()
            );
            $(this).data("val", $(this).val());
        } else {
            $(this).val($(this).data("val"));
        }
    });

$("#input-price-max")
    .on("input", function (e) {
        if (
            $(this).val() >= $("#sidebar-price-slider").slider("values", 0) &&
            $(this).val() <= $("#sidebar-price-slider").slider("option", "max")
        ) {
            $("#sidebar-price-slider").slider(
                "values",
                1,
                $("#input-price-max").val()
            );
            $(this).data("val", $(this).val());
        }
    })
    .on("blur", function (e) {
        if (
            $(this).val() >= $("#sidebar-price-slider").slider("values", 0) &&
            $(this).val() <= $("#sidebar-price-slider").slider("option", "max")
        ) {
            $("#sidebar-price-slider").slider(
                "values",
                1,
                $("#input-price-max").val()
            );
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
        navText: [
            "<i class='fas fa-chevron-left fa-lg dark-pink'></i>",
            "<i class='fas fa-chevron-right fa-lg dark-pink'></i>",
        ],
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
        loop: false,
        center: true,
        margin: 15,
        animateOut: "fadeOut",
        animateIn: "fadeIn",
        nav: true,
        dots: true,
        items: 3,
        navText: [
            "<i class='fas fa-chevron-left fa-lg dark-pink'></i>",
            "<i class='fas fa-chevron-right fa-lg dark-pink'></i>",
        ],
    });

    $("#product-images").on("changed.owl.carousel", function (event) {
        $("#product-detail-thumb").attr(
            "src",
            $(
                $(event.target)
                    .find(".owl-stage .owl-item")
                    .eq(
                        event.item.index >= event.item.count
                            ? 0
                            : event.item.index
                    )[0]
            )
                .find("img")
                .attr("src")
        );
    });

    $("#images").on("change", function (event) {
        const files = event.target.files;
        [...files].forEach((file) => {
            const reader = new FileReader();
            reader.onload = function () {
                $("#product-images")
                    .trigger("add.owl.carousel", [
                        $(
                            `<div class="item new-item col p-0"><input type="hidden" name="images[]" value="${file.name};${reader.result}" /><img class="img-fluid" src="${reader.result}" alt="" /></div>`
                        ),
                    ])
                    .trigger("refresh.owl.carousel");

                $("#product-image-count").text(
                    parseInt($("#product-image-count").attr("value")) +
                        1 +
                        " images"
                );
                $("#product-image-count").attr(
                    "value",
                    parseInt($("#product-image-count").attr("value")) + 1
                );
            };
            reader.readAsDataURL(file);
        });
    });

    $("#delete-icon").on("click", function (event) {
        if ($(".owl-stage .owl-item").length > 1) {
            $("#product-images")
                .trigger(
                    "remove.owl.carousel",
                    $(".owl-stage .owl-item.center.active").index()
                )
                .trigger("refresh.owl.carousel");

            $("#product-image-count").text(
                parseInt($("#product-image-count").attr("value")) -
                    1 +
                    " images"
            );
            $("#product-image-count").attr(
                "value",
                parseInt($("#product-image-count").attr("value")) - 1
            );
        }
    });
});

$("#navbar-mobile li>i").click(function () {
    $(this).toggleClass("rotate-180");
    $(this).parent().siblings().find("ul").slideUp(300);
    $(this).next("ul").stop(true, false, true).slideToggle(300);
    return false;
});

$("#shipping-methods-form input").change(function () {
    $("#shipping-method-price").text(
        "€" + parseFloat($(this).attr("price")).toFixed(2).toString()
    );

    $("#shipping-method-name").text("-- " + $(this).val());

    $("#order-total-price").text(
        "€" +
            (
                parseFloat(
                    $("#payment-methods-form input:checked").attr("price")
                ) +
                parseFloat($(this).attr("price")) +
                parseFloat($("#order-subtotal-price").attr("value"))
            )
                .toFixed(2)
                .toString()
    );
});

$("#payment-methods-form input").change(function () {
    $("#payment-method-price").text(
        "€" + parseFloat($(this).attr("price")).toFixed(2).toString()
    );

    $("#payment-method-name").text("-- " + $(this).val());

    $("#order-total-price").text(
        "€" +
            (
                parseFloat(
                    $("#shipping-methods-form input:checked").attr("price")
                ) +
                parseFloat($(this).attr("price")) +
                parseFloat($("#order-subtotal-price").attr("value"))
            )
                .toFixed(2)
                .toString()
    );
});
