//Settings
var uid = "ddb5af55df81452e8fd8243a32c09885";
var settings = { 
    autostart: 1,
    ui_stop: 0,
    ui_controls: 0,
    ui_help: 0,
    ui_infos: 0,
    ui_watermark: 0,
    ui_hint: 0,
    ui_color: '131C2B',
    transparent: 1
};

var viwer;
var frame = document.getElementById( 'api-frame' );
viwer = new SnIViwer( frame, uid, settings );
viwer.iframe.addEventListener('loadcomplete', OnLoadComplete);
viwer.LoadModel();

function OnLoadComplete()
{
    viwer.SFApi.pause(function(err) {
    });
    viwer.SFApi.setCycleMode('one', function(err) {
        if (!err) {
            window.console.log('Set animation cycle mode');
        }
    });

    /*viwer.SFApi.setAnnotationsTexture({
            url: 'imgLogo.png',
            padding: 2,
            iconSize: 48,
            colNumber: 10
        }, function(err) {
            if (!err) {
            }
    });*/

    viwer.SFApi.addEventListener('annotationSelect', OnHotSpotSelected);
    var event = new Event('visorLoadComplete');
    window.dispatchEvent(event);
}

function OnHotSpotSelected(index)
{
    var hs = viwer.annotations[index];
    if (hs != null && hs.name.includes("AnimHS")) hs = undefined;
    console.log('HotSpot selected: ', hs)
    var event = new CustomEvent('hotspotselected', { detail: { hotspot: hs } });
    window.dispatchEvent(event);
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

