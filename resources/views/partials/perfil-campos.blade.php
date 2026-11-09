{{-- Campos do perfil público do estande (admin e expositor) --}}
<label>Sobre o local</label>
<textarea name="sobre" maxlength="1200" placeholder="Conte um pouco sobre a história, os pratos, o que torna o seu estande especial…">{{ old('sobre', $stand->sobre) }}</textarea>
<div class="row">
  <div><label>Horário de funcionamento</label><input type="text" name="horario" value="{{ old('horario', $stand->horario) }}" placeholder="Ex.: 18h às 00h"></div>
  <div><label>Instagram</label><input type="text" name="instagram" value="{{ old('instagram', $stand->instagram) }}" placeholder="@seurestaurante"></div>
  <div><label>WhatsApp</label><input type="tel" name="whatsapp" value="{{ old('whatsapp', $stand->whatsapp) }}" placeholder="85 99999-9999"></div>
</div>

<h2>Cardápio</h2>
@php $tipoCard = old('cardapio_tipo', ($stand->cardapio_path && ! $stand->cardapio_url) ? 'arquivo' : 'link'); @endphp
<p class="mut">Escolha como mostrar o seu cardápio no app:</p>
<div class="opcoes-card">
  <label class="opcao"><input type="radio" name="cardapio_tipo" value="link" @checked($tipoCard === 'link')> <span><b>Usar um link</b><small>Instagram, site, Google Drive…</small></span></label>
  <label class="opcao"><input type="radio" name="cardapio_tipo" value="arquivo" @checked($tipoCard === 'arquivo')> <span><b>Enviar um arquivo</b><small>PDF ou imagem, até 30 MB</small></span></label>
</div>
<div data-card-bloco="link">
  <label>Link do cardápio</label>
  <input type="url" name="cardapio_url" value="{{ old('cardapio_url', $stand->cardapio_url) }}" placeholder="https://…">
</div>
<div data-card-bloco="arquivo">
  <label>Arquivo do cardápio</label>
  <input type="file" name="cardapio" accept=".pdf,image/*" data-max-mb="30" data-nome="O cardápio">
  <p class="mut dica">Tamanho máximo: 30 MB · PDF, JPG, PNG ou WebP</p>
  <p class="err-t" data-erro-para="cardapio" hidden></p>
  @if($stand->cardapio_path)
    <p class="mut">Arquivo atual: <a href="{{ $stand->cardapioHref() }}" target="_blank">abrir</a>
    · <label class="inline"><input type="checkbox" name="remover_cardapio" value="1"> remover</label></p>
  @endif
</div>

<h2>Fotos <span class="mut">(até 8)</span></h2>
@if($stand->exists && $stand->fotos->count())
  <div class="fotos">
    @foreach($stand->fotos as $f)
      <figure><img src="{{ $f->url() }}" alt=""><label class="mut"><input type="checkbox" name="remover_fotos[]" value="{{ $f->id }}"> remover</label></figure>
    @endforeach
  </div>
@endif
<label>Adicionar fotos</label>
<input type="file" name="fotos[]" accept="image/*" multiple data-max-mb="8" data-max-qtd="{{ 8 - ($stand->exists ? $stand->fotos->count() : 0) }}" data-nome="A foto">
<p class="mut dica">Tamanho máximo: 8 MB por foto · JPG, PNG ou WebP · até 8 fotos por estande</p>
<p class="err-t" data-erro-para="fotos[]" hidden></p>
<p class="mut">A primeira foto aparece como capa no app. As imagens são otimizadas automaticamente.</p>
<style>.opcoes-card{display:grid;gap:8px;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));margin:6px 0 4px}.opcao{display:flex;align-items:flex-start;gap:10px;margin:0;padding:12px 14px;border:1.5px solid #c6d2ee;border-radius:12px;cursor:pointer;font-weight:600}.opcao small{display:block;font-weight:500;color:var(--mut);font-size:12px}.opcao:has(input:checked){border-color:var(--glow);background:var(--soft)}.opcao input{margin-top:3px}[data-card-bloco][hidden]{display:none}input[type=file].invalid{outline:2px solid var(--err);outline-offset:3px;border-radius:6px}.dica{margin:4px 0 0}[data-erro-para]{font-weight:700;margin:6px 0 0}</style>
<script>
(function(){
  var radios=document.querySelectorAll('input[name=cardapio_tipo]');
  function modo(){
    var v=(document.querySelector('input[name=cardapio_tipo]:checked')||{}).value||'link';
    document.querySelectorAll('[data-card-bloco]').forEach(function(b){
      var on=b.dataset.cardBloco===v; b.hidden=!on;
      b.querySelectorAll('input').forEach(function(i){i.disabled=!on;});
    });
  }
  radios.forEach(function(r){r.addEventListener('change',modo);}); modo();
})();
(function(){
  function fmt(b){return b>=1048576?(b/1048576).toFixed(1).replace('.',',')+' MB':Math.ceil(b/1024)+' KB';}
  document.querySelectorAll('input[type=file][data-max-mb]').forEach(function(inp){
    var msg=document.querySelector('[data-erro-para="'+inp.name+'"]');
    function erro(t){inp.classList.toggle("invalid",!!t);if(msg){msg.textContent=t;msg.hidden=!t;}}
    inp.addEventListener('change',function(){
      erro('');
      var max=parseFloat(inp.dataset.maxMb)*1048576, qtd=parseInt(inp.dataset.maxQtd||'0',10), fs=Array.prototype.slice.call(inp.files);
      var grandes=fs.filter(function(f){return f.size>max;});
      if(grandes.length){
        inp.value='';
        erro((grandes.length>1?'Estes arquivos passam':'Este arquivo passa')+' do limite de '+inp.dataset.maxMb+' MB e não foi adicionado: '+grandes.map(function(f){return f.name+' ('+fmt(f.size)+')';}).join(', ')+'. Escolha um arquivo menor.');
        return;
      }
      if(qtd&&fs.length>qtd){
        inp.value='';
        erro('Você pode adicionar mais '+Math.max(qtd,0)+(qtd===1?' foto':' fotos')+' neste estande (limite de 8). Remova alguma foto existente ou escolha menos arquivos.');
      }
    });
  });
})();
</script>
