window.addEventListener('visorLoadComplete', function () {

    const botones = document.querySelectorAll('.viewer-button');

    botones.forEach(boton => {
        const id = obtenerUltimoNumero(boton.id);
        boton.addEventListener('click', () => handleBAnimClick(id-1));
    });

    function handleBAnimClick(id) {
        PlayAnimation(id);
    }

    function obtenerUltimoNumero(cadena) {
        const numeros = cadena.match(/\d+/g);
        return numeros ? parseInt(numeros[numeros.length - 1]) : null;
    }

    window.addEventListener('hotspotselected', function (event) {
        var hotspot = event.detail.hotspot;
        console.log('Selected annotation', hotspot);
    });
});