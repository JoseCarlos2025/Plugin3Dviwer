jQuery(document).ready(function($) {
    window.addEventListener('hotspotselected', function (event) {
        var hotspot = event.detail.hotspot;

        if(hotspot == undefined){
            var entradaID = '0';
        }else{
            var entradaID = hotspot.name.match(/\d+$/)[0];
        }

        $.ajax({
            type: 'POST',
            url: myAjax.ajaxurl,
            data: {
                action: 'cargar_contenido_entrada',
                entry_title: 'contenidovisor'+entradaID
            },
            success: function(response) {
                var contenido = JSON.parse(response);
                $('#content-entry').empty();
                $('#content-entry').append(contenido.contenido);
            }
        });
    });
});
