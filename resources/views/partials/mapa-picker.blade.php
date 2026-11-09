{{-- Seletor visual de posição do estande no mapa. Preenche rect_x/rect_y/rect_w/rect_h do formulário. --}}
<div id="mp" style="margin-top:12px">
  <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-bottom:8px">
    <b>Marcar no mapa</b>
    <button type="button" class="btn sec sm" data-z="-1">−</button>
    <button type="button" class="btn sec sm" data-z="1">+</button>
    <button type="button" class="btn sec sm" id="mp-ir">Ir para o estande</button>
    <span class="mut" id="mp-info"></span>
  </div>
  <p class="mut" style="margin:0 0 8px"><b>Clique</b> num lote para encaixar o estande nele · <b>arraste</b> para desenhar um retângulo livre. Em vermelho: este estande. Tracejado: lotes e outros stands.</p>
  <div id="mp-vp" style="height:520px;overflow:auto;border:1px solid var(--line);border-radius:12px;background:#eaf0fb;touch-action:pan-x pan-y">
    <div id="mp-stage" style="position:relative">
      <img id="mp-img" src="/assets/mapa.svg" alt="" draggable="false" style="display:block;user-select:none">
      <svg id="mp-svg" style="position:absolute;left:0;top:0;cursor:crosshair"></svg>
    </div>
  </div>
</div>
<script>
(function(){
  var D=@json($mapa), W=D.w, H=D.h, NS='http://www.w3.org/2000/svg';
  var form=document.getElementById('mp').closest('form');
  var F={x:form.querySelector('[name=rect_x]'),y:form.querySelector('[name=rect_y]'),w:form.querySelector('[name=rect_w]'),h:form.querySelector('[name=rect_h]')};
  var vp=document.getElementById('mp-vp'),stage=document.getElementById('mp-stage'),img=document.getElementById('mp-img'),svg=document.getElementById('mp-svg'),info=document.getElementById('mp-info');
  var scale=0.5, cur=null, drag=null;
  function el(n,a){var e=document.createElementNS(NS,n);for(var k in a)e.setAttribute(k,a[k]);return e;}
  function val(){return {x:+F.x.value||0,y:+F.y.value||0,w:+F.w.value||0,h:+F.h.value||0};}
  function setVal(r){F.x.value=Math.round(r.x);F.y.value=Math.round(r.y);F.w.value=Math.round(r.w);F.h.value=Math.round(r.h);draw();}
  function layout(){
    img.style.width=(W*scale)+'px';img.style.height=(H*scale)+'px';
    svg.setAttribute('viewBox','0 0 '+W+' '+H);svg.setAttribute('width',W*scale);svg.setAttribute('height',H*scale);
  }
  function draw(){
    while(svg.firstChild)svg.removeChild(svg.firstChild);
    var sw=1.5/scale;
    D.lotes.forEach(function(l){var r=l.rect;svg.appendChild(el('rect',{x:r[0],y:r[1],width:r[2],height:r[3],fill:'rgba(1,52,141,.04)',stroke:'#0a47c9','stroke-width':sw,'stroke-dasharray':(5/scale)+' '+(3/scale),'pointer-events':'none'}));});
    D.landmarks.forEach(function(l){var r=l.rect;svg.appendChild(el('rect',{x:r[0],y:r[1],width:r[2],height:r[3],fill:'none',stroke:'#2e9e6b','stroke-width':sw,'stroke-dasharray':(5/scale)+' '+(3/scale),'pointer-events':'none'}));});
    D.stands.forEach(function(s){var r=s.rect;var e=el('rect',{x:r[0],y:r[1],width:r[2],height:r[3],fill:'rgba(245,166,35,.18)',stroke:'#e58f12','stroke-width':sw,'pointer-events':'none'});var t=el('title',{});t.textContent=s.nome;e.appendChild(t);svg.appendChild(e);});
    var v=val();
    svg.appendChild(el('rect',{x:v.x,y:v.y,width:v.w,height:v.h,fill:'rgba(229,72,77,.35)',stroke:'#e5484d','stroke-width':3/scale,'pointer-events':'none'}));
    if(drag&&drag.moved){var d=drag.r;svg.appendChild(el('rect',{x:d.x,y:d.y,width:d.w,height:d.h,fill:'rgba(229,72,77,.2)',stroke:'#e5484d','stroke-width':2/scale,'stroke-dasharray':(6/scale)+' '+(4/scale),'pointer-events':'none'}));}
    info.textContent='x '+v.x+' · y '+v.y+' · '+v.w+'×'+v.h;
  }
  function pt(ev){var r=svg.getBoundingClientRect();return {x:(ev.clientX-r.left)/scale,y:(ev.clientY-r.top)/scale};}
  function inside(p,r){return p.x>=r[0]&&p.x<=r[0]+r[2]&&p.y>=r[1]&&p.y<=r[1]+r[3];}
  function alvo(p){ // menor retângulo conhecido sob o clique
    var all=[];D.lotes.forEach(function(l){all.push(l.rect);});D.landmarks.forEach(function(l){all.push(l.rect);});D.stands.forEach(function(s){all.push(s.rect);});
    var best=null;all.forEach(function(r){if(inside(p,r)&&(!best||r[2]*r[3]<best[2]*best[3]))best=r;});return best;
  }
  svg.addEventListener('pointerdown',function(ev){svg.setPointerCapture(ev.pointerId);var p=pt(ev);drag={p0:p,sx:ev.clientX,sy:ev.clientY,moved:false,r:null};});
  svg.addEventListener('pointermove',function(ev){
    if(!drag)return;
    if(!drag.moved&&Math.hypot(ev.clientX-drag.sx,ev.clientY-drag.sy)<6)return;
    drag.moved=true;var p=pt(ev),a=drag.p0;
    drag.r={x:Math.min(a.x,p.x),y:Math.min(a.y,p.y),w:Math.abs(p.x-a.x),h:Math.abs(p.y-a.y)};draw();
  });
  svg.addEventListener('pointerup',function(ev){
    if(!drag)return;var d=drag;drag=null;
    if(d.moved&&d.r.w>=8&&d.r.h>=8){setVal(d.r);return;}
    var p=pt(ev),r=alvo(p);
    if(r){setVal({x:r[0],y:r[1],w:r[2],h:r[3]});}
    else{var v=val();setVal({x:Math.max(0,Math.min(W-v.w,p.x-v.w/2)),y:Math.max(0,Math.min(H-v.h,p.y-v.h/2)),w:v.w||80,h:v.h||40});}
  });
  function ir(){var v=val();vp.scrollTo({left:Math.max(0,(v.x+v.w/2)*scale-vp.clientWidth/2),top:Math.max(0,(v.y+v.h/2)*scale-vp.clientHeight/2),behavior:'auto'});}
  document.querySelectorAll('#mp [data-z]').forEach(function(b){b.addEventListener('click',function(){
    var v=val(),cx=(v.x+v.w/2),cy=(v.y+v.h/2);scale=Math.max(0.2,Math.min(1.5,scale*(b.dataset.z==='1'?1.25:0.8)));layout();draw();vp.scrollTo({left:cx*scale-vp.clientWidth/2,top:cy*scale-vp.clientHeight/2});});});
  document.getElementById('mp-ir').addEventListener('click',ir);
  ['x','y','w','h'].forEach(function(k){F[k].addEventListener('input',draw);});
  layout();draw();ir();
})();
</script>
