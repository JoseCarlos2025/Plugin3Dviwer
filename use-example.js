window.addEventListener('visorLoadComplete', function () {

    const botones = document.querySelectorAll('.viewer-button');

    botones.forEach(boton => {
        boton.addEventListener('click', (e) => handleBAnimClick(e));
    });

    function handleBAnimClick(e) {
        PlayAnimation(Number(e.target.getAttribute('data-hotspot') - 1));
    }

    window.addEventListener('hotspotselected', function (event) {
        var hotspot = event.detail.hotspot;
        console.log('Selected annotation', hotspot);
    });
});