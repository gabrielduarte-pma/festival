{{-- Campos do perfil público do stand (admin e expositor) --}}
<label>Sobre o local</label>
<textarea name="sobre" maxlength="1200" placeholder="Conte um pouco sobre a história, os pratos, o que torna o seu stand especial…">{{ old('sobre', $stand->sobre) }}</textarea>
<div class="row">
  <div><label>Horário de funcionamento</label><input type="text" name="horario" value="{{ old('horario', $stand->horario) }}" placeholder="Ex.: 18h às 23h"></div>
  <div><label>Instagram</label><input type="text" name="instagram" value="{{ old('instagram', $stand->instagram) }}" placeholder="@seurestaurante"></div>
  <div><label>WhatsApp</label><input type="tel" name="whatsapp" value="{{ old('whatsapp', $stand->whatsapp) }}" placeholder="85 99999-9999"></div>
</div>

<h2>Cardápio</h2>
<p class="mut">Cole o link do cardápio (Instagram, site, Drive…) <b>ou</b> envie um arquivo (PDF ou imagem, até 10 MB).</p>
<label>Link do cardápio</label>
<input type="url" name="cardapio_url" value="{{ old('cardapio_url', $stand->cardapio_url) }}" placeholder="https://…">
<label>Ou enviar arquivo</label>
<input type="file" name="cardapio" accept=".pdf,image/*">
@if($stand->cardapio_path)
  <p class="mut">Arquivo atual: <a href="{{ $stand->cardapioHref() }}" target="_blank">abrir</a>
  · <label class="inline"><input type="checkbox" name="remover_cardapio" value="1"> remover</label></p>
@endif

<h2>Fotos <span class="mut">(até 8)</span></h2>
@if($stand->exists && $stand->fotos->count())
  <div class="fotos">
    @foreach($stand->fotos as $f)
      <figure><img src="{{ $f->url() }}" alt=""><label class="mut"><input type="checkbox" name="remover_fotos[]" value="{{ $f->id }}"> remover</label></figure>
    @endforeach
  </div>
@endif
<label>Adicionar fotos</label>
<input type="file" name="fotos[]" accept="image/*" multiple>
<p class="mut">A primeira foto aparece como capa no app. As imagens são otimizadas automaticamente.</p>
