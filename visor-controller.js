
// Description: Ejemplo de como cargar un visor de SnI en una pagina web y controlar las animaciones desde botones
var uid = "7d4eb933ce5342a49c0970ae8e6f3909"; //ID del modelo a cargar
var settings = { //Settings del visor
    autostart: 1,
    ui_stop: 0,
    ui_controls: 0,
    ui_help: 0,
    ui_infos: 0,
    ui_watermark: 0,
    ui_hint: 0,
    ui_color: '131C2B',
    transparent: 1,
    hideHotspotsAnnotation: true //Ocultar hotspots (util si ya mostramos informacion de estos en otro panel)
};

//Creamos el visor y lo lanzamos dentro del frame que queramos

var viwer;
var frame = document.getElementById( 'api-frame' );
viwer = new SnIViewer( frame, uid, settings );
viwer.iframe.addEventListener('loadcomplete', OnLoadComplete);
viwer.LoadModel();

//Funcion que se llama cuando el visor esta listo, asignamos los eventos a los botones

function OnLoadComplete()
{
    const botones = document.querySelectorAll('.viewer-button');

    botones.forEach(boton => {
        boton.addEventListener('click', (e) => handleBAnimClick(e));
    });

    function handleBAnimClick(e) {
        PlayAnimation(Number(e.target.getAttribute('data-hotspot') - 1));
    }
    
    window.addEventListener('hotspotselected', function(event) {
        var hotspot = event.detail.hotspot;
        console.log('Selected annotation', hotspot);
    });
}


function PlayAnimation(index)
{
    viwer.PlayAnimation(index, true);
    console.log('Anim play: ', index)
}

function GetAnimationCount()
{
    return viwer.animations.length;
}