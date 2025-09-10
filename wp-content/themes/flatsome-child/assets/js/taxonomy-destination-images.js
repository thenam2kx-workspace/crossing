jQuery(document).ready(function ($) {
    var frame;

    // nút add ảnh
    $(document).on("click", "#nm-add-destination-images", function (e) {
        e.preventDefault();

        if (frame) {
            frame.open();
            return;
        }

        frame = wp.media({
            title: "Chọn ảnh cho Destination",
            button: { text: "Sử dụng ảnh" },
            multiple: true
        });

        frame.on("select", function () {
            var selection = frame.state().get("selection");
            var idsInput = $("#nm-destination-images-ids");
            var current = idsInput.val() ? idsInput.val().split(",") : [];

            selection.map(function (attachment) {
                attachment = attachment.toJSON();
                if (attachment.id) {
                    current.push(attachment.id);
                    $("#nm-selected-destination-images").append(
                        '<div class="nm-thumb" data-id="' + attachment.id + '" style="display:inline-block;margin:5px;position:relative;">' +
                            '<img src="' + attachment.url + '" style="width:80px;height:80px;object-fit:cover;border-radius:6px;" />' +
                            '<button class="nm-remove-image" style="position:absolute;top:2px;right:2px;background:#000;color:#fff;border:none;border-radius:50%;cursor:pointer;">×</button>' +
                        '</div>'
                    );
                }
            });

            idsInput.val(current.join(","));
        });

        frame.open();
    });

    // nút remove ảnh
    $(document).on("click", ".nm-remove-image", function (e) {
        e.preventDefault();
        var parent = $(this).closest(".nm-thumb");
        var id = parent.data("id").toString();
        var idsInput = $("#nm-destination-images-ids");
        var ids = idsInput.val().split(",");
        ids = ids.filter(function (val) { return val !== id; });
        idsInput.val(ids.join(","));
        parent.remove();
    });
});



// document.querySelectorAll('.images-track').forEach(track => {
//     let pos = 0;
//     let speed = 0.5; // tốc độ
//     let playing = false;

//     function animate() {
//         if (playing) {
//         pos -= speed;
//         track.style.transform = `translateX(${pos}px)`;
//         }
//         requestAnimationFrame(animate);
//     }
//     animate();

//     track.closest('.destination-block').addEventListener('mouseenter', () => playing = true);
//     track.closest('.destination-block').addEventListener('mouseleave', () => playing = false);
// });