(function(){
'use strict';
var E=window.EVENTO, DIAS=window.DIAS, STANDS=window.STANDS;
var view=document.getElementById('view');
var STAR='<svg class="star" width="14" height="14" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 0 L14 10 L24 12 L14 14 L12 24 L10 14 L0 12 L10 10 Z" fill="#F5A623"></path></svg>';
function esc(s){return String(s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}
function store(k,v){try{if(v===undefined)return JSON.parse(localStorage.getItem(k));localStorage.setItem(k,JSON.stringify(v));}catch(e){return null;}}
var DEBUG=(function(){try{var q=/[?&]debug=(\d)/.exec(location.search);if(q){localStorage.setItem('debug',q[1]);}return localStorage.getItem('debug')==='1';}catch(e){return false;}})();
function norm(s){return s.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g,'');}

/* ---------- estado do evento ---------- */
function status(){
  var now=Date.now(), ini=new Date(E.inicio).getTime(), fim=new Date(E.fim).getTime();
  if(now<ini){var d=Math.ceil((ini-now)/864e5);return d===1?'Falta 1 dia':'Faltam '+d+' dias';}
  if(now<fim)return 'Acontecendo agora';
  return 'Obrigado por participar';
}
function hojeId(){
  var f=new Intl.DateTimeFormat('pt-BR',{timeZone:'America/Fortaleza',day:'2-digit',month:'2-digit'}).format(new Date());
  var d=DIAS.filter(function(x){return x.data===f;})[0];
  return d?d.id:null;
}

/* ---------- favoritos ---------- */
var favs=store('favs')||{};
function key(dia,a){return dia.id+':'+a.nome;}

/* ---------- views ---------- */
function imgSrc(i){i=String(i||'');return /^(https?:|\/|assets\/)/.test(i)?i:'assets/'+i;}
function avatar(a){return '<div class="avatar"><img class="'+(a.flip?'flip':'')+'" src="'+esc(imgSrc(a.img))+'" alt=""></div>';}

function home(){
  var lu=DIAS.map(function(d){
    return '<div class="lu-day"><div class="d"><b>'+d.nome.toUpperCase()+'</b><span>'+d.data+'</span></div>'+
      d.atracoes.slice().reverse().map(function(a){
        return '<div class="lu-row'+(a.destaque?' hi':'')+'">'+avatar(a)+'<div class="n">'+esc(a.nome)+'</div></div>';
      }).join('')+'</div>';
  }).join('');
  return ''+
  '<section class="hero">'+
    '<img class="plate" src="assets/plate.webp" alt="'+esc(E.nome)+'">'+
    '<div class="eyebrow">9º Festival de<br>Gastronomia e Cultura</div>'+
    '<div class="dates">13, 14 E 15</div>'+
    '<div class="month"><b>DE NOVEMBRO</b> <span>DE 2026</span></div>'+
    '<div class="slogan">Pra <em>todos</em> os <em>gostos.</em></div>'+
    '<div class="pill" id="countdown">'+status()+'</div>'+
  '</section>'+
  '<div class="quick">'+
    '<a class="qcard" href="#programacao"><svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10 H21 M8 3 V7 M16 3 V7"/></svg><div><b>Programação</b><small>3 dias · 6 atrações</small></div></a>'+
    '<a class="qcard" href="#stands"><svg viewBox="0 0 24 24"><path d="M3 9 L5 4 H19 L21 9 Z M4 9 V20 H20 V9 M9 20 V14 H15 V20"/></svg><div><b>Estandes</b><small>'+STANDS.length+' para conhecer</small></div></a>'+
    '<a class="qcard wide" href="#mapa"><svg viewBox="0 0 24 24"><path d="M12 21 C12 21 5 14 5 9.5 A7 7 0 0 1 19 9.5 C19 14 12 21 12 21 Z"/><circle cx="12" cy="9.5" r="2.5"/></svg><div><b>Mapa do evento</b><small>Encontre qualquer estande e veja o caminho</small></div></a>'+
  '</div>'+
  '<div class="sec-head">'+STAR+'LINE-UP'+STAR+'</div>'+lu+
  '<div class="sec-head">'+STAR+'O FESTIVAL'+STAR+'</div>'+
  '<p class="about">'+esc(E.sobre)+'</p>'+
  '<div class="foot"><img src="assets/logo.webp" alt="Prefeitura do Aracati"><span>REALIZAÇÃO</span></div>';
}

var diaSel=null;
function programacao(){
  if(!diaSel)diaSel=hojeId()||DIAS[0].id;
  var dia=DIAS.filter(function(d){return d.id===diaSel;})[0];
  var tabs=DIAS.map(function(d){
    return '<button class="tab" role="tab" data-dia="'+d.id+'" aria-selected="'+(d.id===diaSel)+'"><small>'+d.sigla+'</small><b>'+d.id+'</b></button>';
  }).join('');
  var hi=dia.atracoes.filter(function(a){return a.destaque;})[0]||dia.atracoes[dia.atracoes.length-1];
  var parts=hi.nome.split(' ');var first=parts.shift();
  var feat='<div class="feat"><img class="'+(hi.flip?'flip':'')+'" src="'+esc(imgSrc(hi.img))+'" alt="'+esc(hi.nome)+'">'+
    '<div class="tag">ATRAÇÃO PRINCIPAL</div>'+
    '<div class="name"><b>'+esc(first.toUpperCase())+'</b><span>'+esc(parts.join(' ').toUpperCase())+'</span></div>'+
    '<div class="meta">'+(hi.hora?esc(hi.hora)+' · ':'')+esc(hi.palco)+'</div></div>';
  var list=dia.atracoes.map(function(a){
    var k=key(dia,a), on=!!favs[k];
    return '<div class="slot'+(a===hi?' hi':'')+'">'+
      (a.hora?'<div class="t">'+esc(a.hora)+'</div>':'<div class="t soon">Horário<br>em breve</div>')+
      '<div class="i"><b>'+esc(a.nome)+'</b><small>'+esc(a.palco)+'</small></div>'+
      '<button class="fav" data-fav="'+esc(k)+'" aria-pressed="'+on+'" aria-label="Salvar '+esc(a.nome)+' na minha agenda"><svg viewBox="0 0 24 24"><path d="M12 3 L14.8 9 L21 9.6 L16.4 13.8 L17.8 20 L12 16.8 L6.2 20 L7.6 13.8 L3 9.6 L9.2 9 Z"/></svg></button></div>';
  }).join('');
  return '<h1 class="page-title">Programação</h1><p class="sub">'+dia.nome+', '+dia.data+' · toque na estrela para salvar na sua agenda.</p>'+
    '<div class="tabs" role="tablist">'+tabs+'</div>'+feat+'<div class="list">'+list+'</div>'+
    '<p class="note">Programação sujeita a alterações.</p>';
}

var cat='Todos', q='';
var byName=function(a,b){return a.nome.localeCompare(b.nome,'pt-BR');};
function localTxt(s){
  if(s.ilha)return 'Ilha '+s.ilha+' · posição '+s.pos;
  if(s.num&&s.num!=='s/n')return 'Nº '+s.num;
  return 'Sem número';
}
function standsView(){
  var cats=['Todos'].concat(STANDS.map(function(s){return s.cat;}).filter(function(c,i,a){return a.indexOf(c)===i;}));
  var chips=cats.map(function(c){return '<button class="chip" data-cat="'+esc(c)+'" aria-pressed="'+(c===cat)+'">'+esc(c)+'</button>';}).join('');
  return '<div class="title-row"><h1 class="page-title">Estandes</h1><a class="expo-login" href="/login" aria-label="Área do expositor"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7"/></svg><span>Fazer login</span></a></div><p class="sub" id="count"></p>'+
  '<label class="search"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M16 16 L21 21"/></svg><input id="q" type="search" placeholder="Buscar estande, prato, nº ou ilha" autocomplete="off" value="'+esc(q)+'"></label>'+
  '<div class="chips">'+chips+'</div><div class="list" id="standlist"></div>';
}
function fillStands(){
  var n=norm(q.trim());
  var rows=STANDS.filter(function(s){
    return (cat==='Todos'||s.cat===cat)&&(!n||norm(s.nome).indexOf(n)>-1||norm(localTxt(s)).indexOf(n)>-1||norm(s.cat).indexOf(n)>-1||(s.ref&&norm(s.ref).indexOf(n)>-1));
  }).sort(byName);
  document.getElementById('count').textContent=rows.length+(rows.length===1?' estande':' estandes')+' · toque em "Mapa" para ver onde fica';
  document.getElementById('standlist').innerHTML=rows.length?rows.map(function(s){
    var badge=s.ilha?'<small>Ilha</small><b>'+s.ilha+'</b>':'<small>Nº</small><b>'+esc(s.num&&s.num!=='s/n'?s.num:'—')+'</b>';
    return '<div class="stand tap" data-perfil="'+s.id+'" role="link" tabindex="0"><div class="n">'+badge+'</div><div class="i"><b>'+esc(s.nome)+'</b><small>'+esc(s.cat)+(s.ilha?' · posição '+s.pos:'')+(s.ref?' · perto: '+esc(s.ref):'')+'</small></div>'+
      '<button class="gomap" data-map="'+s.id+'" aria-label="Ver '+esc(s.nome)+' no mapa"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21 C12 21 5 14 5 9.5 A7 7 0 0 1 19 9.5 C19 14 12 21 12 21 Z"/><circle cx="12" cy="9.5" r="2.5"/></svg>Mapa</button></div>';
  }).join(''):'<div class="empty">Nenhum estande encontrado. Tente outro nome ou categoria.</div>';
}

/* ---------- mapa ---------- */
var M=window.MAPA, SVGNS='http://www.w3.org/2000/svg';
var mp={s:1,tx:0,ty:0,sel:null,pos:store('pos'),route:false,gps:null,watch:null,placing:false,vp:null,stage:null,pending:null};
var landByName={}; M.landmarks.forEach(function(l){landByName[l.nome]=l;});

function mapView(){
  var chips=M.landmarks.map(function(l){return '<button class="chip sm" data-land="'+esc(l.nome)+'">'+esc(l.nome)+'</button>';}).join('');
  return ''+
  '<div class="maptop">'+
    '<div class="mrow">'+
    '<label class="search sm"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M16 16 L21 21"/></svg><input id="mq" type="search" placeholder="Buscar estande ou ponto" autocomplete="off"></label>'+
      '<button class="mhelp" data-act="help" aria-label="Como usar o mapa"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 4 L3 6.5 V20 L9 17.5 L15 20 L21 17.5 V4 L15 6.5 Z M9 4 V17.5 M15 6.5 V20"/></svg></button>'+
    '</div>'+
    '<div class="sugg" id="sugg" hidden></div>'+
  '</div>'+
  '<div class="mapvp" id="mapvp"><div class="mapstage" id="mapstage" style="width:'+M.w+'px;height:'+M.h+'px">'+
    '<img src="'+M.src+'" width="'+M.w+'" height="'+M.h+'" alt="Mapa do evento" draggable="false">'+
    '<svg id="mapov" viewBox="0 0 '+M.w+' '+M.h+'" width="'+M.w+'" height="'+M.h+'" aria-hidden="true"></svg>'+
  '</div>'+
  '<div class="mapctl"><button id="zin" aria-label="Aproximar">+</button><button id="zout" aria-label="Afastar">−</button></div>'+
  '<div class="maphint" id="maphint" hidden></div></div>'+
  '<div class="sheet" id="sheet"></div>';
}

function mapApply(){
  var st=mp.stage;
  st.style.transform='translate('+mp.tx+'px,'+mp.ty+'px) scale('+mp.s+')';
  /* Durante o gesto a camada fica "congelada" (rapido); quando para, o will-change sai e o
     navegador redesenha o SVG na escala atual, entao o mapa fica nitido depois do zoom. */
  st.style.willChange='transform';
  clearTimeout(mp.sharpT);
  mp.sharpT=setTimeout(function(){
    st.style.willChange='auto';
    st.style.transform='translate('+mp.tx+'px,'+mp.ty+'px) scale('+mp.s+')';
  },200);
}
function mapClamp(){
  var r=mp.vp.getBoundingClientRect(), w=M.w*mp.s, h=M.h*mp.s;
  var mx=r.width*0.6, my=r.height*0.6;
  mp.tx=Math.min(mx,Math.max(r.width-w-mx,mp.tx));
  mp.ty=Math.min(my,Math.max(r.height-h-my,mp.ty));
}
function mapMinScale(){return mp.vp.getBoundingClientRect().width/780;}
function zoomAt(cx,cy,ns){
  ns=Math.max(mapMinScale()*0.9,Math.min(2.2,ns));
  var k=ns/mp.s;
  mp.tx=cx-(cx-mp.tx)*k; mp.ty=cy-(cy-mp.ty)*k; mp.s=ns; mapClamp(); mapApply();
}
function toMap(clientX,clientY){
  var r=mp.vp.getBoundingClientRect();
  return {x:(clientX-r.left-mp.tx)/mp.s, y:(clientY-r.top-mp.ty)/mp.s};
}
function focusRect(rc,scale){
  var r=mp.vp.getBoundingClientRect();
  mp.s=scale||Math.min(1.1,Math.max(mapMinScale(),r.width/330));
  var cx=rc[0]+rc[2]/2, cy=rc[1]+rc[3]/2;
  mp.tx=r.width/2-cx*mp.s;
  mp.ty=r.height*0.32-cy*mp.s;
  mapClamp(); mapApply();
}

/* ---- GPS / calibragem ---- */
function fitAffine(){
  var A=M.ancoras||[]; if(A.length<2)return null;
  if(A.length===2){ // 2 pontos: escala + rotacao (semelhanca)
    var p=A[0],q=A[1],K=111320,la=(p.lat+q.lat)/2*Math.PI/180;
    var u1=[p.lng*K*Math.cos(la),-p.lat*K],u2=[q.lng*K*Math.cos(la),-q.lat*K];
    var dux=u2[0]-u1[0],duy=u2[1]-u1[1],dwx=q.x-p.x,dwy=q.y-p.y,den=dux*dux+duy*duy; if(!den)return null;
    return {sim:{K:K,la:la,ar:(dwx*dux+dwy*duy)/den,ai:(dwy*dux-dwx*duy)/den,u1:u1,w1:[p.x,p.y]}};
  }
  // x = a*lng + b*lat + c ; y = d*lng + e*lat + f  (minimos quadrados)
  var n=A.length,S=[[0,0,0],[0,0,0],[0,0,0]],bx=[0,0,0],by=[0,0,0];
  A.forEach(function(p){var v=[p.lng,p.lat,1];for(var i=0;i<3;i++){for(var j=0;j<3;j++)S[i][j]+=v[i]*v[j];bx[i]+=v[i]*p.x;by[i]+=v[i]*p.y;}});
  function solve(b){var m=S.map(function(r,i){return r.concat([b[i]]);});
    for(var i=0;i<3;i++){var p=i;for(var k=i+1;k<3;k++)if(Math.abs(m[k][i])>Math.abs(m[p][i]))p=k;var t=m[i];m[i]=m[p];m[p]=t;
      if(Math.abs(m[i][i])<1e-18)return null;
      for(var k=i+1;k<3;k++){var f=m[k][i]/m[i][i];for(var j=i;j<4;j++)m[k][j]-=f*m[i][j];}}
    var x=[0,0,0];for(var i=2;i>=0;i--){var s=m[i][3];for(var j=i+1;j<3;j++)s-=m[i][j]*x[j];x[i]=s/m[i][i];}return x;}
  var cx=solve(bx),cy=solve(by); return cx&&cy?{cx:cx,cy:cy}:null;
}
var AFF=fitAffine();
function mPerUnit(){ // metros por unidade do mapa, a partir das ancoras
  var A=M.ancoras||[]; if(A.length<2)return null;
  function hav(a,b){var R=6371000,t=Math.PI/180,dl=(b.lat-a.lat)*t,dg=(b.lng-a.lng)*t;
    var h=Math.sin(dl/2)*Math.sin(dl/2)+Math.cos(a.lat*t)*Math.cos(b.lat*t)*Math.sin(dg/2)*Math.sin(dg/2);return 2*R*Math.asin(Math.sqrt(h));}
  var tot=0,cnt=0;for(var i=0;i<A.length;i++)for(var j=i+1;j<A.length;j++){var du=Math.hypot(A[i].x-A[j].x,A[i].y-A[j].y);if(du>50){tot+=hav(A[i],A[j])/du;cnt++;}}
  return cnt?tot/cnt:null;
}
function gpsToMap(lat,lng){
  if(AFF&&AFF.sim){var m=AFF.sim,ux=lng*m.K*Math.cos(m.la)-m.u1[0],uy=-lat*m.K-m.u1[1];
    return {x:m.w1[0]+m.ar*ux-m.ai*uy,y:m.w1[1]+m.ar*uy+m.ai*ux};}
  return AFF?{x:AFF.cx[0]*lng+AFF.cx[1]*lat+AFF.cx[2],y:AFF.cy[0]*lng+AFF.cy[1]*lat+AFF.cy[2]}:null;}

/* ---- rota ---- */
function routePts(){
  if(!mp.pos||!mp.sel)return null;
  var t=mp.sel, u=mp.pos, E=M.corredorEsq, D=M.corredorDir, rc=t.rect;
  var tcx=rc[0]+rc[2]/2, tcy=rc[1]+rc[3]/2;
  var side=tcx<(E+D)/2?'L':'R', cT=side==='L'?E:D;
  var cU=Math.abs(u.x-E)<Math.abs(u.x-D)?E:D;
  var edge=side==='L'?rc[0]+rc[2]:rc[0];
  var inside=(tcx>E+60&&tcx<D-60); // ponto central (Palco, Self Point etc.)
  var pts=[[u.x,u.y],[cU,u.y]];
  if(cU!==cT||inside){pts.push([cU,tcy]);}
  if(inside){pts.push([tcx,tcy]);}else{pts.push([cT,tcy]);pts.push([edge,tcy]);}
  // remove duplicados
  return pts.filter(function(p,i){return i===0||Math.hypot(p[0]-pts[i-1][0],p[1]-pts[i-1][1])>1;});
}
function routeLen(p){var s=0;for(var i=1;i<p.length;i++)s+=Math.hypot(p[i][0]-p[i-1][0],p[i][1]-p[i-1][1]);return s;}
function fmtDist(units){var k=mPerUnit();if(!k)return null;var m=Math.round(units*k/5)*5;return m>=1000?(m/1000).toFixed(1).replace('.',',')+' km':m+' m';}
function fmtWalk(units){var k=mPerUnit();if(!k)return null;var min=Math.max(1,Math.round(units*k/75));return min+' min a pé';}

/* ---- overlay ---- */
function el(n,a){var e=document.createElementNS(SVGNS,n);for(var k in a)e.setAttribute(k,a[k]);return e;}
function drawOverlay(){
  var ov=document.getElementById('mapov'); if(!ov)return; ov.innerHTML='';
  var inv=1/Math.max(mp.s,.25);
  if(mp.sel){
    var rc=mp.sel.rect,p=6;
    ov.appendChild(el('path',{d:'M0 0H'+M.w+'V'+M.h+'H0Z M'+(rc[0]-p)+' '+(rc[1]-p)+'v'+(rc[3]+2*p)+'h'+(rc[2]+2*p)+'v-'+(rc[3]+2*p)+'Z','fill-rule':'evenodd',fill:'rgba(1,28,84,.62)'}));
    ov.appendChild(el('rect',{x:rc[0]-p,y:rc[1]-p,width:rc[2]+2*p,height:rc[3]+2*p,rx:6,fill:'none',stroke:'#F5A623','stroke-width':5}));
    var pr=routePts();
    if(mp.route&&pr&&pr.length>1){
      ov.appendChild(el('polyline',{points:pr.map(function(q){return q[0]+','+q[1];}).join(' '),fill:'none',stroke:'#fff','stroke-width':14,'stroke-linecap':'round','stroke-linejoin':'round',opacity:.9}));
      ov.appendChild(el('polyline',{points:pr.map(function(q){return q[0]+','+q[1];}).join(' '),fill:'none',stroke:'#0a47c9','stroke-width':8,'stroke-linecap':'round','stroke-linejoin':'round','stroke-dasharray':'2 16'}));
    }
    // pino no destino
    var cx=rc[0]+rc[2]/2, top=rc[1]-p-4, k=Math.max(1,1.15*inv);
    var g=el('g',{transform:'translate('+cx+','+top+') scale('+k+')'});
    g.appendChild(el('path',{d:'M0 0 C0 0 -22 -26 -22 -42 A22 22 0 0 1 22 -42 C22 -26 0 0 0 0Z',fill:'#F5A623',stroke:'#012468','stroke-width':3}));
    g.appendChild(el('circle',{cx:0,cy:-42,r:8,fill:'#012468'}));
    ov.appendChild(g);
  }
  if(mp.pos){
    var k2=Math.max(1,inv*1.1);
    var mu2=DEBUG&&mp.dbg&&mp.dbg.acc?mPerUnit():null;
    if(mu2)ov.appendChild(el('circle',{cx:mp.pos.x,cy:mp.pos.y,r:mp.dbg.acc/mu2,fill:'rgba(229,72,77,.12)',stroke:'#e5484d','stroke-width':2*k2,'stroke-dasharray':(8*k2)+' '+(5*k2)}));
    ov.appendChild(el('circle',{cx:mp.pos.x,cy:mp.pos.y,r:34*k2,fill:'rgba(10,71,201,.25)'}));
    ov.appendChild(el('circle',{cx:mp.pos.x,cy:mp.pos.y,r:15*k2,fill:'#0a47c9',stroke:'#fff','stroke-width':5*k2}));
  }
}

/* ---- seleção e folha ---- */
function standsAt(rc){
  return STANDS.filter(function(s){return s.rect[0]===rc[0]&&s.rect[1]===rc[1]&&s.rect[2]===rc[2]&&s.rect[3]===rc[3];});
}
function selectStand(id,noFocus){
  var s=STANDS.filter(function(x){return x.id==id;})[0]; if(!s)return;
  mp.sel={rect:s.rect,title:s.nome,items:standsAt(s.rect),num:s.num,ilha:s.ilha,pos:s.pos,ref:s.ref,kind:'stand',focus:s.id};
  afterSelect(noFocus);
}
function selectLand(nome){
  var l=landByName[nome]; if(!l)return;
  mp.sel={rect:l.rect,title:l.nome,items:[],ref:null,kind:'land'};
  afterSelect();
}
function selectLote(lt){
  var its=standsAt(lt.rect);
  mp.sel={rect:lt.rect,title:its.length?its.map(function(x){return x.nome;}).join(' / '):'Lote '+lt.num,items:its,num:lt.num,ref:its[0]&&its[0].ref,kind:its.length?'stand':'lote'};
  afterSelect(true);
}
function afterSelect(noFocus){
  mp.route=false;
  if(!noFocus)focusRect(mp.sel.rect);
  drawOverlay(); renderSheet();
  var h=document.getElementById('maphint'); if(h)h.hidden=true;
}
function clearSel(){mp.sel=null;mp.route=false;drawOverlay();renderSheet();}

function renderSheet(){
  var sh=document.getElementById('sheet'); if(!sh)return;
  if(!mp.sel){
    var gpsOn=mp.watch!=null||store('gps')===true;
    sh.className='sheet idle';
    sh.innerHTML='<div class="sh-row">'+
      '<button class="btn ghost" data-act="where">'+(mp.pos?'Mudar posição':'Marcar onde estou')+'</button></div>'+
      (AFF?(gpsOn?'<div style="margin-top:6px;font-size:12px;opacity:.75;display:flex;justify-content:space-between;align-items:center"><span>📍 Localização ativa</span><button data-act="gpsoff" style="background:none;border:0;color:inherit;text-decoration:underline;font:inherit;padding:4px 0">desligar</button></div>':'<button class="btn" data-act="gps">Usar minha localização</button>'):'');
    return;
  }
  var t=mp.sel, sub=[];
  if(t.kind==='stand'||t.kind==='lote'){
    if(t.ilha)sub.push('Ilha '+t.ilha+' · posição '+t.pos);else if(t.num&&t.num!=='s/n')sub.push('Lote nº '+t.num);
  }
  if(t.ref)sub.push('Perto de '+t.ref);
  var list=t.items.length>1?'<ul class="sh-list">'+t.items.map(function(x){return '<li><a href="#stand/'+x.id+'">'+esc(x.nome)+' <small>'+esc(x.cat)+'</small> <span aria-hidden="true">›</span></a></li>';}).join('')+'</ul>':'';
  var title=t.items.length>1?'Neste ponto ('+t.items.length+' estandes)':t.title;
  var info='';
  var pr=routePts();
  if(mp.route){
    if(pr){var L=routeLen(pr),d=fmtDist(L);info='<div class="sh-route">'+(d?'<b>'+d+'</b> · '+fmtWalk(L)+' · ':'')+'siga a linha azul pontilhada <small>(caminho aproximado)</small></div>';}
    else info='<div class="sh-route">Toque no mapa para marcar onde você está.</div>';
  }
  sh.className='sheet open';
  sh.innerHTML='<button class="sh-x" data-act="close" aria-label="Fechar">×</button>'+
    '<div class="sh-title">'+esc(title)+'</div>'+
    (sub.length?'<div class="sh-sub">'+esc(sub.join(' · '))+'</div>':'')+list+info+
    (t.items.length===1?'<button class="btn prof" data-perfil="'+t.items[0].id+'">Ver perfil do estande</button>':'')+'<div class="sh-btns"><button class="btn" data-act="route">'+(mp.route?'Esconder caminho':'Como chegar')+'</button>'+
    '<button class="btn ghost" data-act="where">'+(mp.pos?'Mudar posição':'Marcar onde estou')+'</button></div>';
}

function setHint(txt){var h=document.getElementById('maphint');if(!h)return;if(DEBUG)txt='';h.textContent=txt||'';h.hidden=!txt;}
function setPos(p,save){mp.pos=p;if(save!==false)store('pos',p);mp.placing=false;setHint('');drawOverlay();renderSheet();}

function startGps(){
  if(!navigator.geolocation){setHint('Seu aparelho não oferece localização.');return;}
  if(mp.watch!=null)return;
  setHint('Buscando sua localização…');
  mp.watch=navigator.geolocation.watchPosition(function(g){
    var p=gpsToMap(g.coords.latitude,g.coords.longitude);
    if(DEBUG){mp.dbg={lat:g.coords.latitude,lng:g.coords.longitude,acc:g.coords.accuracy,t:g.timestamp,n:((mp.dbg&&mp.dbg.n)||0)+1,p:p,head:g.coords.heading,spd:g.coords.speed};}
    if(!p){if(DEBUG)dbgRender();return;}
    var inside=p.x>150&&p.x<M.w-80&&p.y>0&&p.y<M.h;
    if(!inside&&!DEBUG){setHint('Você parece estar fora da área do evento.');mp.pos=null;drawOverlay();return;}
    setHint(inside?'':'Fora da área do evento (modo debug mostra mesmo assim).');mp.gps=true;mp.pos=p;drawOverlay();if(mp.sel)renderSheet();if(DEBUG)dbgRender();
  },function(err){mp.watch=null;setHint('Não consegui sua localização'+(DEBUG?' ('+err.message+')':'')+'. Toque em "Marcar onde estou".');if(DEBUG){mp.dbg={erro:err.message||('código '+err.code),n:0};dbgRender();}},{enableHighAccuracy:true,maximumAge:0,timeout:20000});
}

/* ---- modo debug do GPS (?debug=1 liga, ?debug=0 desliga) ---- */
function dbgNearest(p){
  if(!p)return null;var best=null,mu=mPerUnit()||0.115;
  function chk(nome,r){var cx=r[0]+r[2]/2,cy=r[1]+r[3]/2;var inR=p.x>=r[0]&&p.x<=r[0]+r[2]&&p.y>=r[1]&&p.y<=r[1]+r[3];var d=inR?0:Math.hypot(p.x-cx,p.y-cy)*mu;if(!best||d<best.d)best={nome:nome,d:d,rect:r};}
  STANDS.forEach(function(s){chk(s.nome,s.rect);});
  M.landmarks.forEach(function(l){chk(l.nome,l.rect);});
  return best;
}
function dbgSamples(){return store('dbg_samples')||[];}
function dbgReport(){
  var mu=mPerUnit(),L=['DEBUG GPS '+new Date().toLocaleString(),'ancoras='+JSON.stringify(M.ancoras||[]),'m/unidade='+(mu?mu.toFixed(4):'?'),''];
  dbgSamples().forEach(function(x,i){
    var line=(i+1)+') '+(x.alvo||'(sem alvo)')+' | lat '+x.lat.toFixed(6)+' lng '+x.lng.toFixed(6)+' | ±'+Math.round(x.acc)+'m | mapa x '+(x.px==null?'?':Math.round(x.px))+' y '+(x.py==null?'?':Math.round(x.py));
    if(x.rect&&x.px!=null&&mu){var cx=x.rect[0]+x.rect[2]/2,cy=x.rect[1]+x.rect[3]/2;line+=' | alvo x '+Math.round(cx)+' y '+Math.round(cy)+' | erro '+(Math.hypot(x.px-cx,x.py-cy)*mu).toFixed(1)+' m (dx '+((x.px-cx)*mu).toFixed(1)+' m, dy '+((x.py-cy)*mu).toFixed(1)+' m)';}
    L.push(line);
  });
  return L.join('\n');
}
function dbgRender(){
  var box=document.getElementById('dbg');if(!box)return;var d=mp.dbg,h='';
  if(!d||d.erro){h='aguardando GPS…'+(d&&d.erro?'<br>'+esc(d.erro):'');}
  else{
    var p=d.p,n=dbgNearest(p),ins=p&&p.x>150&&p.x<M.w-80&&p.y>0&&p.y<M.h;
    h='#'+d.n+' · '+new Date(d.t).toLocaleTimeString()+
      '<br>lat <b>'+d.lat.toFixed(6)+'</b> lng <b>'+d.lng.toFixed(6)+'</b>'+
      '<br>precisão ±<b>'+Math.round(d.acc)+' m</b>'+(d.spd!=null?' · '+(d.spd*3.6).toFixed(1)+' km/h':'')+
      '<br>mapa x <b>'+(p?Math.round(p.x):'—')+'</b> y <b>'+(p?Math.round(p.y):'—')+'</b> · '+(p?(ins?'dentro':'FORA da área'):'sem calibração ('+(M.ancoras||[]).length+' pontos)')+
      (n?'<br>mais perto: <b>'+esc(n.nome)+'</b> ('+(n.d<1?'dentro':Math.round(n.d)+' m')+')':'')+
      '<br>alvo: <b>'+esc(mp.sel?mp.sel.title:'toque num estande/lote')+'</b>';
  }
  h+='<div class="dbgb"><button data-d="reg">Registrar ponto</button><button data-d="rep">Relatório ('+dbgSamples().length+')</button><button data-d="clr">Limpar</button><button data-d="off">Sair</button></div><textarea id="dbgt" readonly hidden></textarea>';
  var ta=document.getElementById('dbgt'),keep=ta&&!ta.hidden?ta.value:null;
  h='<div style="display:flex;justify-content:space-between;align-items:center"><b>DEBUG GPS</b><button data-d="min">'+(box.classList.contains('min')?'abrir ▾':'recolher ▴')+'</button></div><div class="dbgbody">'+h+'</div>';
  box.innerHTML=h;
  if(keep!==null){var t2=document.getElementById('dbgt');t2.hidden=false;t2.value=keep;}
}
function dbgInit(){
  if(!DEBUG||document.getElementById('dbg'))return;
  var st=document.createElement('style');st.textContent='#dbg{position:fixed!important;left:6px!important;right:70px!important;z-index:60!important;padding:8px 10px!important;border-radius:10px!important;background:#012468!important;color:#fff!important;font:12px/1.45 ui-monospace,Menlo,Consolas,monospace!important;box-shadow:0 4px 16px rgba(0,0,0,.45)!important;max-height:42vh;overflow:auto;touch-action:auto}#dbg *{font-family:inherit}#dbg b{color:#F5A623!important}#dbg .dbgb{display:flex;flex-wrap:wrap;gap:6px;margin-top:6px}#dbg button{font:700 12px/1 ui-monospace,Menlo,monospace!important;padding:7px 9px!important;border-radius:7px!important;border:0!important;background:#F5A623!important;color:#012468!important;width:auto!important;height:auto!important}#dbg textarea{display:block;width:100%;height:110px;margin-top:6px;font:10px/1.3 ui-monospace,monospace;background:#fff;color:#000;border-radius:6px;border:0}#dbg textarea[hidden]{display:none}#dbg.min .dbgbody{display:none}';document.head.appendChild(st);
  var box=document.createElement('div');box.id='dbg';box.className='dbg';document.body.appendChild(box);
  function pos(){var t=document.querySelector('.maptop');box.style.top=((t?t.getBoundingClientRect().bottom:90)+6)+'px';}pos();window.addEventListener('resize',pos);
  box.addEventListener('click',function(e){
    var a=e.target.getAttribute&&e.target.getAttribute('data-d');if(!a)return;
    if(a==='reg'){
      var d=mp.dbg;if(!d||d.erro){return;}
      var arr=dbgSamples();arr.push({lat:d.lat,lng:d.lng,acc:d.acc,px:d.p?d.p.x:null,py:d.p?d.p.y:null,alvo:mp.sel?mp.sel.title:'',rect:mp.sel?mp.sel.rect:null});store('dbg_samples',arr);dbgRender();
    }else if(a==='rep'){
      var t=document.getElementById('dbgt'),txt=dbgReport();t.hidden=false;t.value=txt;t.focus();t.select();try{navigator.clipboard.writeText(txt).catch(function(){});}catch(x){}
    }else if(a==='clr'){store('dbg_samples',[]);dbgRender();}
    else if(a==='min'){box.classList.toggle('min');dbgRender();}
    else if(a==='off'){try{localStorage.setItem('debug','0');}catch(x){}location.href=location.pathname+'?debug=0#mapa';}
  });
  startGps();dbgRender();
}
function stopGps(){if(mp.watch!=null){navigator.geolocation.clearWatch(mp.watch);mp.watch=null;}}

function mapInit(){
  mp.vp=document.getElementById('mapvp'); mp.stage=document.getElementById('mapstage');
  var r=mp.vp.getBoundingClientRect();
  mp.s=mapMinScale(); mp.tx=-260*mp.s; mp.ty=-40*mp.s; mapClamp(); mapApply();
  drawOverlay(); renderSheet();
  if(DEBUG)dbgInit();else if(AFF&&store('gps')===true)startGps();

  var ptrs={},last=null,moved=false,startPt=null,pinch=null;
  mp.vp.addEventListener('pointerdown',function(e){
    if(e.target.closest('.mapctl,.dbg'))return;
    mp.vp.setPointerCapture(e.pointerId); ptrs[e.pointerId]={x:e.clientX,y:e.clientY};
    var ids=Object.keys(ptrs);
    if(ids.length===1){last={x:e.clientX,y:e.clientY};startPt={x:e.clientX,y:e.clientY};moved=false;}
    else if(ids.length===2){var a=ptrs[ids[0]],b=ptrs[ids[1]];pinch={d:Math.hypot(a.x-b.x,a.y-b.y),s:mp.s};moved=true;}
  });
  mp.vp.addEventListener('pointermove',function(e){
    if(!ptrs[e.pointerId])return; ptrs[e.pointerId]={x:e.clientX,y:e.clientY};
    var ids=Object.keys(ptrs);
    if(ids.length===2&&pinch){
      var a=ptrs[ids[0]],b=ptrs[ids[1]],d=Math.hypot(a.x-b.x,a.y-b.y),rr=mp.vp.getBoundingClientRect();
      zoomAt((a.x+b.x)/2-rr.left,(a.y+b.y)/2-rr.top,pinch.s*d/pinch.d);
    }else if(ids.length===1&&last){
      var dx=e.clientX-last.x,dy=e.clientY-last.y;
      if(!moved&&Math.hypot(e.clientX-startPt.x,e.clientY-startPt.y)>6)moved=true;
      if(moved){mp.tx+=dx;mp.ty+=dy;mapClamp();mapApply();last={x:e.clientX,y:e.clientY};}
    }
  });
  function up(e){
    if(!ptrs[e.pointerId])return; delete ptrs[e.pointerId];
    var n=Object.keys(ptrs).length; if(n<2)pinch=null;
    if(n===0){
      if(!moved&&e.type==='pointerup')mapTap(e.clientX,e.clientY);
      if(moved)drawOverlay();
      last=null;
    }else if(n===1){var k=Object.keys(ptrs)[0];last={x:ptrs[k].x,y:ptrs[k].y};}
  }
  mp.vp.addEventListener('pointerup',up); mp.vp.addEventListener('pointercancel',up);
  mp.vp.addEventListener('wheel',function(e){e.preventDefault();var rr=mp.vp.getBoundingClientRect();zoomAt(e.clientX-rr.left,e.clientY-rr.top,mp.s*(e.deltaY<0?1.15:1/1.15));drawOverlay();},{passive:false});
  document.getElementById('zin').onclick=function(){var rr=mp.vp.getBoundingClientRect();zoomAt(rr.width/2,rr.height/2,mp.s*1.35);drawOverlay();};
  document.getElementById('zout').onclick=function(){var rr=mp.vp.getBoundingClientRect();zoomAt(rr.width/2,rr.height/2,mp.s/1.35);drawOverlay();};

  var mq=document.getElementById('mq'),sg=document.getElementById('sugg');
  mq.addEventListener('input',function(){
    var n=norm(mq.value.trim());
    if(!n){sg.hidden=true;return;}
    var res=STANDS.filter(function(s){return norm(s.nome).indexOf(n)>-1;}).slice(0,6).map(function(s){return {t:'s',id:s.id,nome:s.nome,sub:localTxt(s)};});
    M.landmarks.forEach(function(l){if(norm(l.nome).indexOf(n)>-1)res.unshift({t:'l',nome:l.nome,sub:'Ponto de referência'});});
    res=res.slice(0,7);
    sg.innerHTML=res.length?res.map(function(x){return '<button data-sg="'+x.t+':'+(x.t==='s'?x.id:esc(x.nome))+'"><b>'+esc(x.nome)+'</b><small>'+esc(x.sub)+'</small></button>';}).join(''):'<div class="empty sm">Nada encontrado</div>';
    sg.hidden=false;
  });
}
function mapTap(cx,cy){
  var p=toMap(cx,cy);
  if(mp.placing){setPos({x:Math.round(p.x),y:Math.round(p.y)});return;}
  // pontos de referência
  for(var i=0;i<M.landmarks.length;i++){var l=M.landmarks[i],r=l.rect;if(p.x>=r[0]&&p.x<=r[0]+r[2]&&p.y>=r[1]&&p.y<=r[1]+r[3]){selectLand(l.nome);return;}}
  // stands (ilhas e lotes)
  var hit=null;
  STANDS.forEach(function(s){var r=s.rect;if(p.x>=r[0]-4&&p.x<=r[0]+r[2]+4&&p.y>=r[1]-4&&p.y<=r[1]+r[3]+4)hit=hit||s;});
  if(hit){selectStand(hit.id,true);return;}
  for(var j=0;j<M.lotes.length;j++){var rr=M.lotes[j].rect;if(p.x>=rr[0]&&p.x<=rr[0]+rr[2]&&p.y>=rr[1]&&p.y<=rr[1]+rr[3]){selectLote(M.lotes[j]);return;}}
  if(mp.sel)clearSel();
}
function mapAction(a){
  if(a==='close')clearSel();
  else if(a==='help'){var hh=document.getElementById('maphint');if(hh&&!hh.hidden&&mp.helpOn){mp.helpOn=false;setHint('');}else{mp.helpOn=true;setHint('Toque num lote ou escolha um estande. Arraste para mover e use dois dedos para aproximar.');clearTimeout(mp.helpT);mp.helpT=setTimeout(function(){if(mp.helpOn){mp.helpOn=false;setHint('');}},8000);}}
  else if(a==='route'){mp.route=!mp.route;if(mp.route&&!mp.pos){mp.placing=true;setHint('Toque no mapa onde você está agora');}drawOverlay();renderSheet();}
  else if(a==='where'){mp.placing=true;setHint('Toque no mapa onde você está agora');}
  else if(a==='gps'){store('gps',true);startGps();renderSheet();}
  else if(a==='gpsoff'){store('gps',false);stopGps();mp.gps=false;mp.pos=null;setHint('');drawOverlay();renderSheet();}
}


/* ---------- perfil do stand ---------- */
var PERFIS=window.PERFIS||{};
function standById(id){return STANDS.filter(function(x){return String(x.id)===String(id);})[0];}
function igUrl(v){if(!v)return null;v=String(v).trim();if(/^https?:/i.test(v))return v;return 'https://instagram.com/'+v.replace(/^@/,'');}
function waUrl(v){var n=String(v||'').replace(/\D/g,'');return n?'https://wa.me/'+n:null;}
var CATICON={Gastronomia:'M7 3v8a2 2 0 0 0 2 2v8 M11 3v8 M9 3v8 M17 3c-2 2-2 6 0 8v10',Bebidas:'M8 3h8l-1 8a3 3 0 0 1-6 0Z M12 14v7 M8 21h8',Artesanato:'M12 3l2.5 5.5L20 9l-4 4 1 6-5-3-5 3 1-6-4-4 5.5-.5Z',Cultura:'M5 8l7-4 7 4v2H5Z M7 10v8 M12 10v8 M17 10v8 M4 20h16',Serviços:'M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7Z M9 12l2 2 4-4',Outros:'M12 3l2.5 5.5L20 9l-4 4 1 6-5-3-5 3 1-6-4-4 5.5-.5Z'};
function perfilView(id){
  var s=standById(id); if(!s)return '<div class="empty">Estande não encontrado.</div><p style="text-align:center"><a class="btn" href="#stands">Ver estandes</a></p>';
  var P=PERFIS[s.id]||{}, fotos=P.fotos||(P.foto?[P.foto]:[]);
  var ico=CATICON[s.cat]||CATICON.Outros;
  var hero=fotos.length?'<div class="pf-photos" aria-label="Fotos de '+esc(s.nome)+'">'+fotos.map(function(f){return '<img src="'+esc(f)+'" alt="Foto de '+esc(s.nome)+'" loading="lazy">';}).join('')+'</div>'
    :'<div class="pf-photo-empty"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="'+ico+'"/></svg><b>Foto em breve</b></div>';
  var loc=s.ilha?'Ilha '+s.ilha+' · posição '+s.pos:(s.num&&s.num!=='s/n'?'Lote nº '+s.num:'Sem número');
  var cardapio=P.cardapio?'<a class="btn" href="'+esc(P.cardapio)+'" target="_blank" rel="noopener">Ver cardápio</a>':'<span class="btn off" aria-disabled="true">Cardápio em breve</span>';
  var ig=igUrl(P.instagram), instag=ig?'<a class="btn ghost" href="'+esc(ig)+'" target="_blank" rel="noopener">Instagram</a>':'<span class="btn ghost off" aria-disabled="true">Instagram em breve</span>';
  var wa=waUrl(P.whatsapp), zap=wa?'<a class="btn ghost" href="'+esc(wa)+'" target="_blank" rel="noopener">WhatsApp</a>':'';
  var mesmo=STANDS.filter(function(x){return x.id!==s.id&&x.rect[0]===s.rect[0]&&x.rect[1]===s.rect[1]&&x.rect[2]===s.rect[2]&&x.rect[3]===s.rect[3];});
  return '<button class="pf-back" data-back="1" aria-label="Voltar"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>Voltar</button>'+
  hero+
  '<div class="eyebrow pf-cat">'+esc(s.cat)+'</div>'+
  '<h1 class="page-title pf-title">'+esc(s.nome)+'</h1>'+
  '<div class="pf-chips"><span class="chip on">'+esc(loc)+'</span>'+(s.ref?'<span class="chip on">Perto de '+esc(s.ref)+'</span>':'')+(P.horario?'<span class="chip on">'+esc(P.horario)+'</span>':'')+'</div>'+
  '<p class="pf-about">'+(P.sobre?esc(P.sobre):'Em breve mais informações sobre este estande.')+'</p>'+
  '<div class="pf-actions">'+cardapio+instag+zap+'</div>'+
  '<div class="pf-actions two"><button class="btn ghost" data-map="'+s.id+'">Ver no mapa</button><button class="btn" data-goroute="'+s.id+'">Como chegar</button></div>'+
  (mesmo.length?'<div class="sec-head" style="margin-top:26px">NO MESMO PONTO</div><div class="list">'+mesmo.map(function(x){return '<a class="stand" href="#stand/'+x.id+'"><div class="i"><b>'+esc(x.nome)+'</b><small>'+esc(x.cat)+'</small></div></a>';}).join('')+'</div>':'');
}

/* ---------- roteamento ---------- */
var pendingMap=null, pendingRoute=false;
var routes={inicio:home,programacao:programacao,stands:standsView,mapa:mapView};
function render(){
  var name=(location.hash||'#inicio').slice(1), arg=null;
  if(name.indexOf('stand/')===0){arg=name.slice(6);name='perfil';}
  if(name!=='perfil'&&!routes[name])name='inicio';
  view.innerHTML=name==='perfil'?perfilView(arg):routes[name]();
  if(name==='stands')fillStands();
  document.body.classList.toggle('mapmode',name==='mapa');
  if(name==='mapa'){mapInit();if(pendingMap){var pm=pendingMap;pendingMap=null;selectStand(pm);if(pendingRoute){pendingRoute=false;mapAction('route');}}}else stopGps();
  document.querySelectorAll('.tabbar a').forEach(function(a){
    if(a.dataset.tab===(name==='perfil'?'stands':name))a.setAttribute('aria-current','page');else a.removeAttribute('aria-current');
  });
  if(name!=='mapa')window.scrollTo(0,0);
}
window.addEventListener('hashchange',render);

document.addEventListener('click',function(e){
  if(e.target.closest('[data-back]')){if(history.length>1)history.back();else location.hash='#stands';return;}
  var gr=e.target.closest('[data-goroute]');
  if(gr){pendingMap=gr.dataset.goroute;pendingRoute=true;location.hash='#mapa';return;}
  var gm=e.target.closest('[data-map]');
  if(gm){pendingMap=gm.dataset.map;location.hash='#mapa';return;}
  var pf=e.target.closest('[data-perfil]');
  if(pf){location.hash='#stand/'+pf.dataset.perfil;return;}
  var ac=e.target.closest('[data-act]');
  if(ac){mapAction(ac.dataset.act);return;}
  var ld=e.target.closest('[data-land]');
  if(ld){selectLand(ld.dataset.land);return;}
  var sg=e.target.closest('[data-sg]');
  if(sg){var v=sg.dataset.sg.split(':');document.getElementById('sugg').hidden=true;document.getElementById('mq').value='';if(v[0]==='s')selectStand(v[1]);else selectLand(v.slice(1).join(':'));return;}
  var t=e.target.closest('[data-dia]');
  if(t){diaSel=t.dataset.dia;render();return;}
  var f=e.target.closest('[data-fav]');
  if(f){var k=f.dataset.fav;if(favs[k])delete favs[k];else favs[k]=1;store('favs',favs);f.setAttribute('aria-pressed',String(!!favs[k]));return;}
  var c=e.target.closest('[data-cat]');
  if(c){cat=c.dataset.cat;document.querySelectorAll('.chip').forEach(function(x){x.setAttribute('aria-pressed',String(x===c));});fillStands();}
});
document.addEventListener('input',function(e){if(e.target.id==='q'){q=e.target.value;fillStands();}});

render();
setInterval(function(){var c=document.getElementById('countdown');if(c)c.textContent=status();},60000);
if('serviceWorker' in navigator&&location.protocol.indexOf('http')===0){navigator.serviceWorker.register('sw.js').catch(function(){});}
})();
